document.addEventListener('DOMContentLoaded', function() {
    var icons = document.querySelectorAll('.material-icons');
    icons.forEach(function(icon) {
        icon.classList.add('loaded');
    });

    var startChatFormContainer = document.getElementById('startChatFormContainer');
    if (startChatFormContainer) {
        var poweredByContainer = document.createElement('div');
        poweredByContainer.style.display = 'flex';
        poweredByContainer.style.alignItems = 'center';
        poweredByContainer.style.justifyContent = 'center';
        poweredByContainer.style.margin = '5px 10px 15px';

        var icon = document.createElement('img');
        icon.src = aionAssists.iconUrl;
        icon.style.width = '20px';
        icon.style.height = '20px';
        icon.style.marginRight = '5px';

        var poweredByText = document.createElement('span');
        poweredByText.textContent = 'powered by ';
        poweredByText.style.fontSize = '12px';
        poweredByText.style.marginRight = '2px';
        poweredByText.style.color = '#000916';

        var link = document.createElement('a');
        link.href = 'http://aionisys.com';
        link.textContent = 'aionisys.com';
        link.style.fontWeight = 'bold';
        link.style.fontSize = '12px';
        link.style.color = '#000916';
        link.target = '_blank';

        poweredByContainer.appendChild(icon);
        poweredByContainer.appendChild(poweredByText);
        poweredByContainer.appendChild(link);

        startChatFormContainer.appendChild(poweredByContainer);
    }

    var chatInputArea = document.getElementById('chatbot-input-area');
    if (chatInputArea) {
        var inputAndButtonContainer = document.createElement('div');
        inputAndButtonContainer.style.display = 'flex';
        inputAndButtonContainer.style.alignItems = 'center';

        var textarea = chatInputArea.querySelector('textarea');
        inputAndButtonContainer.appendChild(textarea);

        var sendButton = chatInputArea.querySelector('button');
        inputAndButtonContainer.appendChild(sendButton);

        var poweredByContainer = document.createElement('div');
        poweredByContainer.style.display = 'flex';
        poweredByContainer.style.alignItems = 'center';
        poweredByContainer.style.justifyContent = 'center';
        poweredByContainer.style.marginTop = '10px';

        var icon = document.createElement('img');
        icon.src = aionAssists.iconUrl;
        icon.style.width = '20px';
        icon.style.height = '20px';
        icon.style.marginRight = '5px';

        var poweredByText = document.createElement('span');
        poweredByText.textContent = 'powered by ';
        poweredByText.style.fontSize = '12px';
        poweredByText.style.marginRight = '2px';
        poweredByText.style.color = '#000916';

        var link = document.createElement('a');
        link.href = 'http://aionisys.com';
        link.textContent = 'aionisys.com';
        link.style.fontWeight = 'bold';
        link.style.fontSize = '12px';
        link.style.color = '#000916';
        link.target = '_blank';

        poweredByContainer.appendChild(icon);
        poweredByContainer.appendChild(poweredByText);
        poweredByContainer.appendChild(link);

        chatInputArea.appendChild(inputAndButtonContainer);
        chatInputArea.appendChild(poweredByContainer);
    }
});

jQuery(document).ready(function($) {
    loadChatState();
    $('.chat-start-button').click(function(e) {
        e.preventDefault();

        const userName = encodeURIComponent($('#userName').val());
        var userEmail = $('#userEmail').val();
        var isValid = true;

        if (!userName) {
            $('#userName').addClass('input-error');
            isValid = false;
        } else {
            $('#userName').removeClass('input-error');
        }

        if (!userEmail || !userEmail.match(/^[^ ]+@[^ ]+\.[a-z]+$/)) {
            $('#userEmail').addClass('input-error');
            isValid = false;
        } else {
            $('#userEmail').removeClass('input-error');
        }

        if (!isValid) {
            return;
        }
        $('.chat-start-button').prop('disabled', true);
        var firstMessage = $(this).data('message');
        var originalButtonText = $(this).text();
        $(this).prop('disabled', true);
        $(this).html(typingIndicatorHtml);

        saveChatState(userName, userEmail, firstMessage);
        startChatSession(userName, userEmail, firstMessage, originalButtonText, this);

    });

    $('#userName, #userEmail').focus(function() {
        $(this).removeClass('input-error');
    });

    var typingIndicatorHtml = "<div class='ticontainer'><div class='tiblock'><div class='tidot'></div><div class='tidot'></div><div class='tidot'></div></div></div>";

    function appendFirstMessage() {
        if (aionAssists.firstMessage) {
            var firstMessageHtml = "<div class='chatbot-first-message'>";
            if (aionAssists.firstMessageHref && aionAssists.firstMessageHref !== '#') {
                firstMessageHtml += "<a href='" + aionAssists.firstMessageHref + "' style='color: #a5a5a5; text-decoration: none; font-size: 12px;'>" + aionAssists.firstMessage + "</a>";
            } else {
                firstMessageHtml += aionAssists.firstMessage;
            }
            firstMessageHtml += "</div>";
            $('#chatbot-messages').prepend(firstMessageHtml);
        }
    }

    function startChatSession(userName, userEmail, firstMessage, originalButtonText, button) {
        $.ajax({
            url: 'https://aionassistsapp.azurewebsites.net/customer/start-session',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                userName: userName,
                userEmail: userEmail
            }),
            success: function(response) {
                appendFirstMessage();
                var sessionId = response.sessionId;
                localStorage.setItem('sessionId', sessionId);
                setTimeout(function() {
                    $(button).html(originalButtonText).prop('disabled', false);
                }, 5000);
                saveChatState(userName, userEmail);
                postMessage('https://aionassistsapp.azurewebsites.net/customer', firstMessage, userName, userEmail);
            },
            error: function() {
                $(button).html(originalButtonText).prop('disabled', false);
                alert('Chat başlatılamadı.');
            }
        });
    }


    function postMessage(apiUrl, message, userName, userEmail) {
        saveChatState(userName, userEmail);
        var headers = getHeaders();
        let previousMessages = JSON.parse(localStorage.getItem('chatMessages')) || [];
        let localTime = new Date().toISOString().slice(0, 23).replace('T', ' ');

        if (previousMessages.length > 4) {
            previousMessages = previousMessages.slice(-4);
        }

        $.ajax({
            url: apiUrl,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                message: message,
                previousMessages: previousMessages,
                localTime: localTime
            }),
            headers: headers,
            beforeSend: function() {
                appendTypingIndicator();

            },
            success: function(response) {

                $('#chatContent').show();
                $('#startChatFormContainer').hide();
                removeTypingIndicator();

                appendMessageToChat(message, true);
                appendMessageToChat(response.responseText, false);
                setupMessageSending(apiUrl, headers, userEmail);
                saveMessageToLocalStorage('user', message);
                saveMessageToLocalStorage('assistant', response.responseText);
            },
            error: function() {
                alert('Mesaj gönderilemedi.');
            }
        });
    }

    function setupMessageSending(apiUrl, headers, userEmail) {
        $('#chatbot-input').keypress(function(e) {
            if (e.which == 13) {
                sendMessage(apiUrl, headers, userEmail);
                e.preventDefault();
            }
        });

        $('#chatbot-send-button').click(function() {
            sendMessage(apiUrl, headers, userEmail);
        });
    }

    function sendMessage(apiUrl) {
        let localTime = new Date().toISOString().slice(0, 23).replace('T', ' ');
        let previousMessages = JSON.parse(localStorage.getItem('chatMessages')) || [];
        if (previousMessages.length > 4) {
            previousMessages = previousMessages.slice(-4);
        }
        var userMessage = $('#chatbot-input').val();
        if (userMessage.trim() !== '') {
            appendMessageToChat(userMessage, true);
            $('#chatbot-input').val('').attr('disabled', true);
            appendTypingIndicator();
            var headers = getHeaders();

            $.ajax({
                url: apiUrl,
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    message: userMessage,
                    previousMessages: previousMessages,
                    localTime: localTime
                }),
                headers: headers,
                success: function(response) {
                    removeTypingIndicator();
                    appendMessageToChat(response.responseText, false);
                    $('#chatbot-input').attr('disabled', false);
                    saveMessageToLocalStorage('user', userMessage);
                    saveMessageToLocalStorage('assistant', response.responseText);
                },
                error: function() {
                    removeTypingIndicator();
                    appendMessageToChat('Mesaj gönderilemedi.', false);
                    $('#chatbot-input').attr('disabled', false);
                }
            });
        }
    }

    function saveMessageToLocalStorage(role, content) {
        let messages = JSON.parse(localStorage.getItem('chatMessages')) || [];
        messages.push({
            role: role,
            content: content
        });
        if (messages.length > 8) {
            messages.shift();
        }
        localStorage.setItem('chatMessages', JSON.stringify(messages));
    }


    function saveChatState(userName, userEmail) {
        var messages = $('#chatbot-messages').html();
        localStorage.setItem('chatbotMessages', messages);
        localStorage.setItem('name', userName);
        localStorage.setItem('email', userEmail);
        localStorage.setItem('chatStarted', true);
    }

    function loadChatState() {
        var sessionId = localStorage.getItem('sessionId');
        var userName = localStorage.getItem('name');
        var userEmail = localStorage.getItem('email');
        var messages = localStorage.getItem('chatbotMessages');
        var chatStarted = localStorage.getItem('chatStarted') === 'true';

        if (chatStarted && userName && userEmail) {
            $('#chatContent').show();
            $('#startChatFormContainer').hide();

            $('#chatbot-messages').html(messages);

            $('#chatbot-messages .chatbot-bot-message').each(function() {
                var $message = $(this);
                var backgroundImage = $message.attr('data-background-image');
                var color = $message.attr('data-color');

                if (backgroundImage) {
                    $message.css('background-image', backgroundImage);
                }
                if (color) {
                    $message.css('color', color);
                }
            });

            var apiUrl = 'https://aionassistsapp.azurewebsites.net/customer';
            var sessionId = localStorage.getItem('sessionId');

            if (sessionId) {
                setupMessageSending(apiUrl, getHeaders(), userEmail);
            }
        }
    }

    function appendTypingIndicator() {
        var typingIndicatorHtml = "<div class='chatbot-message chatbot-bot-message'><div class='ticontainer'><div class='tiblock'><div class='tidot'></div><div class='tidot'></div><div class='tidot'></div></div></div></div>";
        $('#chatbot-messages').append(typingIndicatorHtml);
        scrollToBottom();
    }

    function removeTypingIndicator() {
        $('.ticontainer').parent('.chatbot-message.chatbot-bot-message').remove();
    }

    var selectedTheme = aionAssists.selectedTheme;
    var themeStyles = {
        'theme1': {
            'backgroundImage': 'linear-gradient(135deg, #CE9FFC 10%, #938aed 100%)'
        },
        'theme2': {
            'backgroundImage': 'linear-gradient(135deg, #FF9D6C 10%, #dd6d95 100%)'
        },
        'theme3': {
            'backgroundImage': 'linear-gradient( 135deg, #3C8CE7 50%, #00EAFF 130%)'
        },
        'theme4': {
            'backgroundImage': 'linear-gradient(-225deg, #7085B6 0%, #87A7D9 50%, #a2bec5 100%)'
        },
        'theme5': {
            'backgroundImage': 'linear-gradient( 109.6deg,  rgba(61,131,97,1) 11.2%, rgba(28,103,88,1) 91.1% )'
        },
        'theme6': {
            'backgroundImage': 'radial-gradient(circle at 18.7% 27.8%, rgb(233 233 241 / 50%) 0%, rgb(255, 255, 255) 90%)',
            'color': '#5b5b5b'
        }
    };

    function appendMessageToChat(message, isUser) {
        message = message.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');

        message = message.replace(/\[(.+?)\]\((.+?)\)/g, '<a href="$2" target="_blank">$1</a>');

        var messageClass = isUser ? 'chatbot-user-message' : 'chatbot-bot-message';
        var messageHtml = "<div class='chatbot-message " + messageClass + "'>" + message + "</div>";

        $('#chatbot-messages').append(messageHtml);

        if (!isUser) {
            var themeStyle = themeStyles[selectedTheme];
            if (themeStyle.backgroundImage) {
                $('.chatbot-bot-message').last().css('background-image', themeStyle.backgroundImage);
                $('.chatbot-bot-message').last().attr('data-background-image', themeStyle.backgroundImage);
            }
            if (themeStyle.color) {
                $('.chatbot-bot-message').last().css('color', themeStyle.color);
                $('.chatbot-bot-message').last().attr('data-color', themeStyle.color);
            }
        }

        var updatedChatbotMessages = $('#chatbot-messages').html();
        localStorage.setItem('chatbotMessages', updatedChatbotMessages);

        scrollToBottom();
    }



    function getHeaders() {
        var userName = localStorage.getItem('name');
        var userEmail = localStorage.getItem('email');
        var sessionId = localStorage.getItem('sessionId');

        return {
            'Authorization': 'Bearer ' + aionAssists.apiToken,
            'name': userName,
            'email': userEmail,
            'sessionId': sessionId
        };
    }

    $('#chatbot-open-button').click(function() {
        $('#chatbot-box').show();
        $('#chatbot-toggle').hide();
        scrollToBottom();
    });

    $('.close-form-btn').click(function() {
        $('#chatbot-box').hide();
        $('#chatbot-toggle').show();
    });

    $('#close-start-chat-form-button').click(function() {
        $('#chatbot-box').hide();
        $('#chatbot-toggle').show();
    });

    $('#minimize-chat').click(function() {
        $('#chatbot-box').hide();
        $('#chatbot-toggle').show();
    });

    $('#chatbot-close-button').click(function() {
        $('#chatbot-confirmation').addClass('chatbot-confirmation-show');
    });

    $('#confirm-close').click(function() {
        localStorage.clear();
        $('#chatbot-messages').empty();
        $('#chatContent').hide();
        $('#startChatFormContainer').show();
        $('#chatbot-box').hide();
        $('#chatbot-toggle').show();
        $('#userName').val('');
        $('#userEmail').val('');
        $('.chat-start-button').prop('disabled', false);
        $('#chatbot-confirmation').removeClass('chatbot-confirmation-show');
    });

    $('#cancel-close').click(function() {
        $('#chatbot-confirmation').removeClass('chatbot-confirmation-show');
    });

    function scrollToBottom() {
        var messagesContainer = $('#chatbot-messages');
        messagesContainer.scrollTop(messagesContainer.prop('scrollHeight'));
    }

    $('#chatbot-input').on('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
});