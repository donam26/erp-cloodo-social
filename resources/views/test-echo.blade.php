<!DOCTYPE html>
<html>
<head>
    <title>Test Echo</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @vite(['resources/js/app.js'])
    <style>
        #messages {
            height: 400px;
            overflow-y: auto;
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 20px;
        }
        .message {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        .message.received {
            background-color: #e9ecef;
        }
        .message.sent {
            background-color: #007bff;
            color: white;
            margin-left: 20%;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Test Echo - Conversation {{ $conversation_id }}</h1>
        
        <div id="messages"></div>
        
        <form id="message-form">
            <div class="form-group">
                <input type="text" id="message-input" class="form-control" placeholder="Nhập tin nhắn...">
            </div>
            <button type="submit" class="btn btn-primary">Gửi</button>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            // Thiết lập CSRF token cho AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Lắng nghe sự kiện từ Echo
            window.Echo.channel(`conversation.{{ $conversation_id }}`)
                .listen('MessageSent', (e) => {
                    console.log('New message:', e);
                    appendMessage(e.message, 'received');
                });

            // Xử lý form gửi tin nhắn
            $('#message-form').on('submit', function(e) {
                e.preventDefault();
                
                const content = $('#message-input').val();
                if (!content) return;

                // Gửi tin nhắn qua API
                $.ajax({
                    url: '/api/messages',
                    method: 'POST',
                    data: {
                        content: content,
                        conversation_id: '{{ $conversation_id }}'
                    },
                    success: function(response) {
                        $('#message-input').val('');
                        appendMessage(response.data, 'sent');
                    },
                    error: function(error) {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi gửi tin nhắn');
                    }
                });
            });

            // Hàm thêm tin nhắn vào khung chat
            function appendMessage(message, type) {
                const messageHtml = `
                    <div class="message ${type}">
                        <div class="content">${message.content}</div>
                        <small class="time">${new Date(message.created_at).toLocaleString()}</small>
                    </div>
                `;
                $('#messages').append(messageHtml);
                $('#messages').scrollTop($('#messages')[0].scrollHeight);
            }
        });
    </script>
</body>
</html> 