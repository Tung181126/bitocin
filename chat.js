// Hàm gửi tin nhắn đến server
function sendMessage() {
    var messageInput = document.getElementById('message');
    var message = messageInput.value;
    messageInput.value = ''; // Xóa trường nhập sau khi gửi

    // Kiểm tra nếu tin nhắn không rỗng
    if (message.trim() === '') {
        return; // Nếu rỗng, không làm gì cả
    }

    // Tạo một đối tượng XMLHttpRequest mới
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'send_message.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    // Xử lý phản hồi từ server
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Cập nhật giao diện hoặc thông báo người dùng
            console.log(xhr.responseText);
        }
    };

    // Gửi yêu cầu với dữ liệu
    xhr.send('message=' + encodeURIComponent(message));
}

// Hàm để gửi tệp
function sendFile() {
    var fileInput = document.getElementById('file');
    var file = fileInput.files[0];
    if (!file) return;

    // Tạo một đối tượng FormData mới
    var formData = new FormData();
    formData.append('file', file);

    // Tạo một đối tượng XMLHttpRequest mới
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'send_message.php', true); // Đảm bảo gửi đến send_message.php

    // Xử lý phản hồi từ server
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Cập nhật giao diện hoặc thông báo người dùng
            console.log(xhr.responseText);
        }
    };

    // Gửi yêu cầu với tệp
    xhr.send(formData);
}

// Ensure the DOM is fully loaded before executing the script
document.addEventListener('DOMContentLoaded', function() {
    // Hàm để tải tin nhắn mới từ server
    function loadMessages() {
        // Tạo một đối tượng XMLHttpRequest mới
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_messages.php?username=' + encodeURIComponent(username), true);

        // Xử lý phản hồi từ server
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Phân tích cú pháp chuỗi JSON thành một mảng các đối tượng
                var messages = JSON.parse(xhr.responseText);

                // Lấy phần tử chat-box
                var chatBox = document.getElementById('chat-box');

                // Xóa nội dung hiện tại của chat-box
                chatBox.innerHTML = '';

                // Tạo một phần tử HTML cho mỗi tin nhắn và thêm vào chat-box
                messages.forEach(function(message) {
                    var messageDiv = document.createElement('div');
                    // Thêm class tương ứng vào div tin nhắn
                    messageDiv.className = message.sender === username ? 'message-user' : 'message-admin';
                    messageDiv.innerHTML = `
                        <p><strong>${message.sender}:</strong> ${message.text}</p>
                        ${message.image_url ? `<img src="${message.image_url}" alt="Image" style="max-width: 100%;">` : ''}
                        <p>${new Date(message.created_at).toLocaleString()}</p>
                    `;
                    chatBox.appendChild(messageDiv);
                });

                // Cuộn đến tin nhắn cuối cùng
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        };

        // Gửi yêu cầu
        xhr.send();
    }

    // Gọi hàm loadMessages để tải tin nhắn khi trang được tải
    loadMessages();

    // Có thể thiết lập để tải lại tin nhắn sau mỗi khoảng thời gian nhất định
    setInterval(loadMessages, 5000); // Tải lại tin nhắn mỗi 5 giây
});

// Hàm để tải lịch sử trò chuyện từ server
function loadChatHistory() {
    // Tạo một đối tượng XMLHttpRequest mới
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'get_chat_history.php?username=' + encodeURIComponent(username), true);

    // Xử lý phản hồi từ server
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Cập nhật nội dung của chat-history với lịch sử trò chuyện
            document.getElementById('chat-history').innerHTML = xhr.responseText;
        }
    };

    // Gửi yêu cầu
    xhr.send();
}