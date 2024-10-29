document.addEventListener('DOMContentLoaded', function() {

    const licenseTypeFree = document.getElementById('licenseTypeFree');
    const licenseTypePremium = document.getElementById('licenseTypePremium');
    const freeTrialCheckbox = document.getElementById('free_trial_started');

    const licenseKeyInput = document.getElementById('license_key');
    const openAIKeyInput = document.getElementById('openai_key');

    function firstInputValues() {
        if (aionAssistsSettings.licenseOption === "free") {
            jQuery('#licenseTypeFree').prop('checked', true);
            licenseKeyInput.value = generateLicenseKey();
            licenseKeyInput.readOnly = true;
        } else {
            jQuery('#licenseTypePremium').prop('checked', true);
            openAIKeyInput.disabled = true;
            openAIKeyInput.value = 'Premium users do not need to input.';
        }
    }

    function updateFormFields() {
        if (licenseTypeFree.checked) {
            openAIKeyInput.disabled = false;
            openAIKeyInput.value = '';
            openAIKeyInput.required = true;
            licenseKeyInput.readOnly = true;
            licenseKeyInput.value = generateLicenseKey();
            licenseKeyInput.disabled = false;
            freeTrialCheckbox.checked = false;
        } else if (licenseTypePremium.checked) {
            openAIKeyInput.disabled = true;
            openAIKeyInput.value = 'Premium users do not need to input.';
            licenseKeyInput.value = '';
            licenseKeyInput.readOnly = false;
            licenseKeyInput.disabled = false;
            freeTrialCheckbox.checked = false;
        }
    }

    firstInputValues();

    if (licenseTypeFree) {
        licenseTypeFree.addEventListener('change', updateFormFields);
    }
    if (licenseTypePremium) {
        licenseTypePremium.addEventListener('change', updateFormFields);
    }

    freeTrialCheckbox.addEventListener('change', function() {
        if (freeTrialCheckbox.checked) {
            licenseTypePremium.checked = true;
            licenseKeyInput.value = 'Free trial selected.';
            licenseKeyInput.disabled = true;
            openAIKeyInput.disabled = true;
            openAIKeyInput.value = 'Premium users do not need to input.';
        } else {
            updateFormFields();
        }
    });

    function generateLicenseKey() {
        const prefix = 'fu_';
        const keyLength = 32 - prefix.length;
        const characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        let randomString = '';

        for (let i = 0; i < keyLength; i++) {
            const index = Math.floor(Math.random() * characters.length);
            randomString += characters[index];
        }

        return prefix + randomString;
    }

    licenseTypeFree.addEventListener('change', updateFormFields);
    licenseTypePremium.addEventListener('change', updateFormFields);

});

var isFetchSessionsCalled = false;

jQuery(document).ready(function($) {

    if (aionAssistsSettings.onDashboardPage) {
        fetchSessionData();

        let userPlan = '';

        function fetchSessionData() {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'fetch_sessions_data',
                },
                success: function(response) {
                    userPlan = response.data.status;

                    if (userPlan === 'advanced') {
                        fetchPremiumSessionData();
                    } else if (userPlan === 'enterprise') {
                        fetchPremiumSessionData();
                        if (!isFetchSessionsCalled) {
                            fetchSessions();
                            isFetchSessionsCalled = true;
                        }
                    } else {
                        updateSessionMetric(aionAssistsSettings.sessionCount);
                    }
                    fetchMessagesForSession();
                }
            });
        }

        function fetchPremiumSessionData() {
            const licenseId = aionAssistsSettings.licenseId;
            $.ajax({
                url: 'https://aionassistsapp.azurewebsites.net/auth/sessions',
                type: 'POST',
                contentType: 'application/json',
                headers: {
                    'Authorization': 'Bearer ' + aionAssistsSettings.apiToken
                },
                success: function(data) {
                    updateSessionMetric(data.sessions);
                    updateMessagesMetric(data.messages);
                    updateSuccessfulValueMetric(data.successful);
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching premium session data: ", error);
                }
            });
        }

        function updateSessionMetric(sessions) {
            $('#sessions-metric').text(sessions);
        }

        function updateMessagesMetric(messages) {
            $('#conversions-metric').text(messages);
        }

        function updateSuccessfulValueMetric(successfulValue) {
            $('#successful-metric').text(successfulValue);
        }

        function fetchSessions() {
            $.ajax({
                url: 'https://aionassistsapp.azurewebsites.net/auth/get-sessions',
                type: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + aionAssistsSettings.apiToken
                },
                success: function(sessions) {
                    const sessionsContainer = document.querySelector('.left-column');
                    sessions.forEach(session => {
                        const sessionButton = document.createElement('button');
                        sessionButton.classList.add('session-button');
                        sessionButton.innerHTML = `
					<div>${aionAssistsSettings.sessionIdHeader} ${session.id}</div>
                    <div>${session.customerName}</div>
                    <div>${session.email}</div>
                    <div>${formatDate(session.createdAt)}</div>
                `;
                        sessionButton.onclick = function() {
                            fetchMessagesForSession(session.id);
                        };
                        sessionsContainer.appendChild(sessionButton);
                    });
                },
                error: function(error) {
                    console.error('Error fetching sessions:', error);
                }
            });
        }

        function formatDate(isoDateString) {
            const date = new Date(isoDateString);
            return date.toLocaleString('en-GB', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });
        }

        function fetchMessagesForSession(sessionId) {
            const licenseId = aionAssistsSettings.licenseId;
            const planName = aionAssistsSettings.planName;

            const rightColumn = document.querySelector('.right-column');

            const planAlert = document.getElementById('plan-alert');

            if (planName === 'enterprise') {
                $.ajax({
                    url: `https://aionassistsapp.azurewebsites.net/auth/get-messages?sessionId=${sessionId}`,
                    type: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + aionAssistsSettings.apiToken
                    },
                    success: function(data) {
                        rightColumn.innerHTML = '';

                        if (data.messages.length > 0) {
                            const headerDiv = document.createElement('div');
                            headerDiv.classList.add('session-header');
                            headerDiv.innerHTML = `
                        <h3>${aionAssistsSettings.sessionDetailsHeader}</h3>
                        <p><strong>${aionAssistsSettings.sessionIdHeader}</strong> ${sessionId}</p>
                        <p><strong>${aionAssistsSettings.customerHeader}</strong> ${data.messages[0].customerName} (${data.messages[0].email})</p>
                        <p><strong>${aionAssistsSettings.sessionStartedHeader}</strong> ${formatDate(data.messages[0].createdAt)}</p>
                    `;
                            const summaryButton = document.createElement('button');
                            summaryButton.innerHTML = `<img src="${aionAssistsSettings.summaryIconUrl}" alt="Icon" height="24px"> ${aionAssistsSettings.showSummaryText}`;
                            summaryButton.onclick = function() {
                                fetchMessageSummary(sessionId, licenseId);
                                this.onclick = null;
                                this.classList.add('disabled');
                            };
                            headerDiv.appendChild(summaryButton);
                            summaryButton.classList.add('summary-button');

                            rightColumn.appendChild(headerDiv);
                        }

                        data.messages.forEach(message => {
                            const messageDiv = document.createElement('div');
                            messageDiv.classList.add('message');
                            messageDiv.innerHTML = `
                        <div><strong>${message.customerName}:</strong> ${message.sendedMessage}</div>
                        <div class="message-date">${formatDate(message.createdAt)}</div>
                    `;
                            rightColumn.appendChild(messageDiv);
                        });
                    },
                    error: function(error) {
                        console.error('Error fetching messages:', error);
                    }
                });
            } else {
                const alertBox = document.createElement('div');
                alertBox.className = 'alert-box';
                alertBox.innerHTML = `  
        <p> ` + aionAssistsSettings.needUpgradeToEnterprise + ` </p><br>
        <a href="https://aionisys.com/pricing">` + aionAssistsSettings.checkoutLink + ` </a>
    `;
                rightColumn.innerHTML = '';
                rightColumn.appendChild(alertBox);
            }

        }

        function fetchMessageSummary(sessionId, licenseId) {
            $.ajax({
                url: `https://aionassistsapp.azurewebsites.net/auth/get-messages-summary?sessionId=${sessionId}&licenseId=${licenseId}`,
                type: 'GET',
                success: function(data) {
                    const summaryDiv = document.createElement('div');
                    summaryDiv.classList.add('session-summary');
                    summaryDiv.innerHTML = `<p><strong>${aionAssistsSettings.sessionSummaryHeader}</strong> ${data.summarize}</p>`;

                    let headerDiv = document.querySelector('.session-header');
                    if (!headerDiv) {
                        headerDiv = document.createElement('div');
                        headerDiv.classList.add('session-header');
                        document.querySelector('.right-column').prepend(headerDiv);
                    }
                    headerDiv.appendChild(summaryDiv);
                },
                error: function(error) {
                    console.error('Error fetching summary:', error);
                }
            });
        }
    }

function saveCompanyDetails(e) {
    e.preventDefault();
    
    const button = jQuery('#complete-installation');
    const loader = button.find('.lds-roller');
    const buttonText = button.contents().filter(function() {
        return this.nodeType === 3;
    });
    
    button.prop('disabled', true);
    loader.show(); 
    buttonText.each(function() {
        this.nodeValue = ''; 
    });

    const licenseOption = jQuery('input[name="license_option"]:checked').val();
    const licenseKey = jQuery('#license_key').val();
    const openaiKey = jQuery('#openai_key').val();
    const companyName = jQuery('#company_name').val();
    const websiteUrl = jQuery('#website_url').val();
    const language = aionAssistsSettings.siteLanguage;

    const postData = {
        license_option: licenseOption,
        license_key: licenseKey,
        openai_key: openaiKey,
        company_name: companyName,
        website_url: websiteUrl,
        language: language,
    };

    if (licenseKey.trim() === '') {
        alert('Please enter a valid License Key');
        button.prop('disabled', false); 
        loader.hide();
        buttonText.each(function() {
            this.nodeValue = 'Complete Installation'; 
        });
        return;
    }

    jQuery.ajax({
        url: 'https://aionassistsapp.azurewebsites.net/auth', // Diğer sunucunun URL'sini belirtin
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(postData),
        success: function(response) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    'action': 'save_company_details',
                    'token': response.token,
                    'license_option': licenseOption,
                    'license_key': licenseKey,
                    'license_id': response.licenseId,
                    'openai_key': openaiKey,
                    'company_name': companyName,
                    'plan_name' : response.planName
                },
                success: function(response) {
                    jQuery('#setupWizard').submit();
                },
                error: function() {
                    alert('Error saving company details');
                    button.prop('disabled', false); 
                    loader.hide(); 
                    buttonText.each(function() {
                        this.nodeValue = 'Complete Installation'; 
                    });
                }
            });
        },
        error: function(xhr) {
            button.prop('disabled', false);
            loader.hide(); 
            buttonText.each(function() {
                this.nodeValue = 'Complete Installation'; 
            });
        }
    });
}

jQuery('#complete-installation').click(saveCompanyDetails);
   

    function saveSettings() {
        var formData = {
            'retrieve_an_order_status': $('input[name="retrieve_an_order_status"]:checked').val(),
            'retrieve_billing_address': $('input[name="retrieve_billing_address"]:checked').val(),
            'retrieve_shipping_address': $('input[name="retrieve_shipping_address"]:checked').val(),
            'retrieve_an_order_create_date': $('input[name="retrieve_an_order_create_date"]:checked').val(),
            'retrieve_an_order_items': $('input[name="retrieve_an_order_items"]:checked').val(),
            'retrieve_order_refunds': $('input[name="retrieve_order_refunds"]:checked').val(),
            'list_all_payment_gateways': $('input[name="list_all_payment_gateways"]:checked').val(),
            'list_all_shipping_zones': $('input[name="list_all_shipping_zones"]:checked').val(),
            'update_an_order_note': $('input[name="update_an_order_note"]:checked').val(),
            'update_shipping_address': $('input[name="update_shipping_address"]:checked').val(),
            'describe_product_marketing_style': $('input[name="describe_product_marketing_style"]:checked').val(),
            'retrieve_product_stock': $('input[name="retrieve_product_stock"]:checked').val(),
        };

        $.ajax({
            url: 'https://aionassistsapp.azurewebsites.net/auth/settings',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            headers: {
                'Authorization': 'Bearer ' + aionAssistsSettings.apiToken
            },
            success: function(response) {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        'action': 'aionassists_save_settings',
                        ...formData
                    },
                    success: function(response) {
                        showSnackbar("Settings saved successfully.", "success");
                    },
                    error: function() {
                        showSnackbar("There was a problem saving the settings.", "error");
                    }
                });
            },
            error: function(xhr, status, error) {
                if (xhr.status === 402) {
                    var errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : "Error: Unable to save settings.";
                    showSnackbar(errorMessage, "error");
                } else {
                    console.error('Error:', error);
                    showSnackbar("An unexpected error occurred.", "error");
                }
            }
        });
    }
    jQuery('#save-settings').off('click').click(saveSettings);

    function savePreferences() {
        var formData = {
            courierCompany: $('#courierCompany').val(),
            returnConditions: $('#returnConditions').val(),
            warrantyTerms: $('#warrantyTerms').val(),
            dispatchDuration: $('#dispatchDuration').val(),
            phoneNumber: $('#phoneNumber').val(),
            emailAddress: $('#emailAddress').val(),
            freeShippingThreshold: $('#freeShippingThreshold').val(),
            refundDuration: $('#refundDuration').val()
        };

        $.ajax({
            url: 'https://aionassistsapp.azurewebsites.net/auth/preferences',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            headers: {
                'Authorization': 'Bearer ' + aionAssistsSettings.apiToken
            },
            success: function(response) {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        'action': 'aionassists_save_preferences',
                        'courierCompany': formData.courierCompany,
                        'returnConditions': formData.returnConditions,
                        'warrantyTerms': formData.warrantyTerms,
                        'dispatchDuration': formData.dispatchDuration,
                        'phoneNumber': formData.phoneNumber,
                        'emailAddress': formData.emailAddress,
                        'freeShippingThreshold': formData.freeShippingThreshold,
                        'refundDuration': formData.refundDuration
                    },
                    success: function(response) {
                        showSnackbar("Preferences saved successfully.", "success");
                    },
                    error: function() {
                        showSnackbar("There was a problem saving the settings.", "error");
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }
    jQuery('#save-preferences').off('click').click(savePreferences);

    function saveMainSettings() {

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                'action': 'aionassists_save_main_settings',
                'welcomeHeader': $('#welcomeHeader').val(),
                'welcomeMessageText': $('#welcomeMessageText').val(),
                'chatbotHeader': $('#chatbotHeader').val(),
                'faqHref': $('#faqHref').val(),
                'firstMessage': $('#firstMessage').val(),
                'firstMessageHref': $('#firstMessageHref').val(),
                'selectedTheme': $('input[name="aionassists_theme"]:checked').val(),
            },
            success: function(response) {
                showSnackbar("Customize settings saved successfully.", "success");
            },
            error: function() {
                showSnackbar("There was a problem saving the settings.", "error");
            }
        });
    }
    jQuery('#save-main-settings').click(saveMainSettings);

    function showSnackbar(message, status) {
        var x = document.getElementById("snackbar");
        x.innerText = message;
        x.className = '';
        if (status === 'success') {
            x.classList.add('success');
        } else if (status === 'error') {
            x.classList.add('error');
        }
        x.classList.add('show');
        setTimeout(function() {
            x.classList.remove("show");
        }, 5000);
    }


});