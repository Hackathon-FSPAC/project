{{--<!DOCTYPE html>--}}
{{--<html lang="en">--}}
{{--<head>--}}
{{--    <meta charset="UTF-8">--}}
{{--    <title>Simple AI Chatbot</title>--}}
{{--    <style>--}}
{{--        body { font-family: sans-serif; padding: 2rem; max-width: 600px; margin: auto; }--}}
{{--        input, button { padding: 10px; margin-top: 10px; width: 100%; }--}}
{{--        #chat-box { margin-top: 20px; background: #f1f1f1; padding: 10px; border-radius: 5px; }--}}
{{--    </style>--}}
{{--</head>--}}
{{--<body>--}}
{{--<h2>Chat with AI (Gemini)</h2>--}}

{{--<form id="chat-form">--}}
{{--    <input type="text" id="message" placeholder="Type your message..." required />--}}
{{--    <button type="submit">Send</button>--}}
{{--</form>--}}

{{--<div id="chat-box"></div>--}}

{{--<script>--}}
{{--    const form = document.getElementById('chat-form');--}}
{{--    const messageInput = document.getElementById('message');--}}
{{--    const chatBox = document.getElementById('chat-box');--}}

{{--    form.addEventListener('submit', async (e) => {--}}
{{--        e.preventDefault();--}}
{{--        const msg = messageInput.value;--}}
{{--        chatBox.innerHTML += `<p><strong>You:</strong> ${msg}</p>`;--}}
{{--        messageInput.value = '';--}}

{{--        const res = await fetch('/chatbot/talk', {--}}
{{--            method: 'POST',--}}
{{--            headers: {--}}
{{--                'Content-Type': 'application/json',--}}
{{--                'X-CSRF-TOKEN': '{{ csrf_token() }}'--}}
{{--            },--}}
{{--            body: JSON.stringify({ message: msg })--}}
{{--        });--}}

{{--        const data = await res.json();--}}
{{--        chatBox.innerHTML += `<p><strong>AI:</strong> ${data.reply}</p>`;--}}
{{--    });--}}
{{--</script>--}}
{{--</body>--}}
{{--</html>--}}


    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Financial AI Chatbot</title>
    <style>
        body { font-family: sans-serif; padding: 2rem; max-width: 600px; margin: auto; }
        input, button { padding: 10px; margin-top: 10px; }
        #chat-form { display: flex; }
        #message { flex-grow: 1; margin-right: 10px; }
        #send-button { width: 80px; }
        #chat-box {
            margin-top: 20px;
            background: #f1f1f1;
            padding: 10px;
            border-radius: 5px;
            height: 400px;
            overflow-y: auto;
        }
        .clear-button {
            margin-top: 10px;
            background: #f44336;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            padding: 8px 12px;
        }
        .user-message {
            background: #e1f5fe;
            padding: 8px 12px;
            border-radius: 18px;
            margin: 8px 0;
            max-width: 80%;
            align-self: flex-end;
            margin-left: auto;
        }
        .ai-message {
            background: #f5f5f5;
            padding: 8px 12px;
            border-radius: 18px;
            margin: 8px 0;
            max-width: 80%;
        }
        .message-container {
            display: flex;
            flex-direction: column;
        }
    </style>
</head>
<body>
<h2>Financial AI Assistant</h2>

@auth
    <p>Welcome back! Your conversation history is being saved.</p>
    <button id="clear-history" class="clear-button">Clear History</button>
@else
    <p>Sign in to save your conversation history.</p>
@endauth

<div id="chat-box" class="message-container">
    @if(isset($messages) && count($messages) > 0)
        @foreach($messages as $message)
            <div class="{{ $message->role == 'user' ? 'user-message' : 'ai-message' }}">
                <strong>{{ $message->role == 'user' ? 'You' : 'AI' }}:</strong> {!! nl2br(e($message->content)) !!}
            </div>
        @endforeach
    @endif
</div>

<form id="chat-form">
    <input type="text" id="message" placeholder="Type your message..." required />
    <button type="submit" id="send-button">Send</button>
</form>

<script>
    const form = document.getElementById('chat-form');
    const messageInput = document.getElementById('message');
    const chatBox = document.getElementById('chat-box');
    const clearButton = document.getElementById('clear-history');

    // Scroll to bottom of chat box when page loads
    chatBox.scrollTop = chatBox.scrollHeight;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const msg = messageInput.value;
        if (!msg.trim()) return;

        // Add user message to chat
        const userMessageDiv = document.createElement('div');
        userMessageDiv.className = 'user-message';
        userMessageDiv.innerHTML = `<strong>You:</strong> ${msg}`;
        chatBox.appendChild(userMessageDiv);

        // Scroll to bottom of chat
        chatBox.scrollTop = chatBox.scrollHeight;

        messageInput.value = '';
        messageInput.disabled = true;
        document.getElementById('send-button').disabled = true;

        try {
            const res = await fetch('/chatbot/talk', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ message: msg })
            });

            const data = await res.json();

            // Add AI response to chat
            const aiMessageDiv = document.createElement('div');
            aiMessageDiv.className = 'ai-message';
            aiMessageDiv.innerHTML = `<strong>AI:</strong> ${data.reply.replace(/\n/g, '<br>')}`;
            chatBox.appendChild(aiMessageDiv);

            // Scroll to bottom of chat
            chatBox.scrollTop = chatBox.scrollHeight;
        } catch (error) {
            console.error('Error:', error);

            // Add error message to chat
            const errorDiv = document.createElement('div');
            errorDiv.className = 'ai-message';
            errorDiv.innerHTML = '<strong>AI:</strong> Sorry, there was an error processing your request.';
            chatBox.appendChild(errorDiv);
        } finally {
            messageInput.disabled = false;
            document.getElementById('send-button').disabled = false;
            messageInput.focus();
        }
    });

    // Clear history functionality
    if (clearButton) {
        clearButton.addEventListener('click', async () => {
            if (confirm('Are you sure you want to clear your conversation history?')) {
                try {
                    const res = await fetch('/chatbot/clear', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    const data = await res.json();
                    if (data.status === 'success') {
                        chatBox.innerHTML = '';
                    }
                } catch (error) {
                    console.error('Error clearing history:', error);
                }
            }
        });
    }
</script>
</body>
</html>
