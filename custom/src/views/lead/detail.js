define('custom:views/lead/detail', ['views/detail'], function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);
            
            this.addMenuItem('buttons', {
                label: 'Find contacts',
                action: 'findContacts',
                acl: 'read'
            });
        },

        actionFindContacts: function () {
            var id = this.model.id;
            
            if (!id) {
                Espo.Ui.notify('Lead ID is missing.', 'error');
                return;
            }

            Espo.Ui.notify('Searching for contacts...', 'info');

            Espo.Ajax.postRequest('Lead/action/findContacts', {id: id})
                .then(function (response) {
                    if (response.success) {
                        var message = response.message;
                        var contacts = response.contacts || [];
                        
                        if (contacts.length > 0) {
                            var contactList = contacts.map(function (contact) {
                                return contact.name + ' (' + contact.emailAddress + ')';
                            }).join(', ');
                            
                            message += ' ' + contactList;
                        }
                        
                        Espo.Ui.notify(message, 'success');
                    } else {
                        Espo.Ui.notify(response.message || 'An error occurred.', 'error');
                    }
                })
                .catch(function (error) {
                    var errorMessage = error.responseJSON?.message || error.message || 'An error occurred while searching for contacts.';
                    Espo.Ui.notify(errorMessage, 'error');
                });
        }

    });
});

