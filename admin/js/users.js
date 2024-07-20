document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners for other actions (delete, edit, lock, etc.)
    document.querySelectorAll('.btn-primary').forEach(function(button) {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId; // Assuming the button has a data-user-id attribute

            // Handle delete action
            Swal.fire({
                icon: 'warning',
                title: 'Xoá users',
                text: 'Bạn có chắc chắn muốn xoá người dùng này?',
                showCancelButton: true,
                confirmButtonText: 'Xoá',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send delete request to server
                    fetch('delete_user.php', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `user_id=${userId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: 'Người dùng đã được xoá',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Thất bại',
                                text: 'Không thể xoá người dùng',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Đã xảy ra lỗi',
                            confirmButtonText: 'OK'
                        });
                        console.error('Error:', error);
                    });
                }
            });
        });
    });

    // Handle edit action
    document.querySelectorAll('.btn-edit').forEach(function(button) {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;

            // Fetch user data
            fetch(`get_user.php?user_id=${userId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('edit-username').value = data.user.username;
                        document.getElementById('edit-email').value = data.user.email;
                        document.getElementById('edit-phone').value = data.user.phone;
                        document.getElementById('edit-balance').value = data.user.balance;
                        document.getElementById('save-changes').dataset.userId = userId;
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: data.message,
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Đã xảy ra lỗi khi lấy dữ liệu người dùng',
                        confirmButtonText: 'OK'
                    });
                    console.error('Error:', error);
                });
        });
    });

    // Save changes
    document.getElementById('save-changes').addEventListener('click', function() {
        const userId = this.dataset.userId;
        const username = document.getElementById('edit-username').value;
        const email = document.getElementById('edit-email').value;
        const phone = document.getElementById('edit-phone').value;
        const balance = document.getElementById('edit-balance').value;

        // Send update request to server
        fetch('update_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `user_id=${userId}&username=${username}&email=${email}&phone=${phone}&balance=${balance}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công',
                    text: 'Người dùng đã được cập nhật',
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Thất bại',
                    text: 'Không thể cập nhật người dùng',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: 'Đã xảy ra lỗi',
                confirmButtonText: 'OK'
            });
            console.error('Error:', error);
        });
    });

    // Handle lock/unlock actions
    document.querySelectorAll('.btn-toggle-lock').forEach(function(button) {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const lockType = this.dataset.lockType;
            let url = '';

            switch (lockType) {
                case 'account':
                    url = 'toggle_account_lock.php';
                    break;
                case 'withdrawal':
                    url = 'toggle_withdrawal_lock.php';
                    break;
                case 'deposit':
                    url = 'toggle_deposit_lock.php';
                    break;
                default:
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Invalid lock type',
                        confirmButtonText: 'OK'
                    });
                    return;
            }

            // Send lock/unlock request to server
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `user_id=${userId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: 'Trạng thái đã được cập nhật',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Thất bại',
                        text: 'Không thể cập nhật trạng thái',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Đã xảy ra lỗi',
                    confirmButtonText: 'OK'
                });
                console.error('Error:', error);
            });
        });
    });
});