function sendAdminMessage() {
    var messageInput = document.getElementById('admin-message');
    var message = messageInput.value;
    messageInput.value = ''; // Xóa trường nhập sau khi gửi

    if (message.trim() === '') {
        return; // Nếu rỗng, không làm gì cả
    }

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'send_admin_message.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            console.log(xhr.responseText);
            // Có thể cập nhật giao diện chat tại đây
        }
    };
    xhr.send('message=' + encodeURIComponent(message));
}

// Có thể thêm hàm loadMessages tương tự như ở chat.js để cập nhật tin nhắn