$(document).ready(function() {
    function loadDeposits() {
        $.ajax({
            url: 'get_deposits.php',
            method: 'GET',
            success: function(data) {
                const deposits = JSON.parse(data);
                let tableBody = '';
                deposits.forEach(deposit => {
                    tableBody += `
                        <tr>
                            <td>${deposit.id}</td>
                            <td>${deposit.user_id}</td>
                            <td>${deposit.username}</td>
                            <td>${deposit.amount}</td>
                            <td>${deposit.status === 'pending' ? '<i class="fas fa-spinner fa-spin"></i> Chờ xử lí' : deposit.status}</td>
                            <td>
                                <button class="btn btn-success approve-deposit-btn" data-id="${deposit.id}">Phê Duyệt</button>
                                <button class="btn btn-danger reject-deposit-btn" data-id="${deposit.id}">Từ Chối</button>
                                <button class="btn btn-info view-proof-btn" data-image="${deposit.image_path}">Xem Bằng Chứng</button>
                            </td>
                        </tr>
                    `;
                });
                $('#deposit-table-body').html(tableBody);

                // Attach event handlers
                $('.approve-deposit-btn').off('click').on('click', function() {
                    const id = $(this).data('id');
                    updateDeposit(id, 'approve');
                });

                $('.reject-deposit-btn').off('click').on('click', function() {
                    const id = $(this).data('id');
                    updateDeposit(id, 'reject');
                });

                $('.view-proof-btn').off('click').on('click', function() {
                    const imagePath = $(this).data('image');
                    $('#proofImage').attr('src', imagePath);
                    $('#proofModal').modal('show');
                });
            }
        });
    }

    function loadTransactions() {
        $.ajax({
            url: 'get_transactions.php',
            method: 'GET',
            success: function(data) {
                const transactions = JSON.parse(data);
                renderTransactions(transactions);
            }
        });
    }

    function renderTransactions(transactions) {
        let tableBody = '';
        transactions.forEach(transaction => {
            tableBody += `
                <tr>
                    <td>${transaction.id}</td>
                    <td>${transaction.user_id}</td>
                    <td>${transaction.username}</td>
                    <td>${transaction.account_id}</td>
                    <td>${transaction.amount}</td>
                    <td>${transaction.status === 'pending' ? '<i class="fas fa-spinner fa-spin"></i> Chờ xử lí' : transaction.status}</td>
                    <td>
                        <button class="btn btn-success approve-btn" data-id="${transaction.id}">Phê Duyệt</button>
                        <button class="btn btn-danger reject-btn" data-id="${transaction.id}">Từ Chối</button>
                    </td>
                </tr>
            `;
        });
        $('#transaction-table-body').html(tableBody);

        // Attach event handlers
        $('.approve-btn').off('click').on('click', function() {
            const id = $(this).data('id');
            updateTransaction(id, 'approve');
        });

        $('.reject-btn').off('click').on('click', function() {
            const id = $(this).data('id');
            updateTransaction(id, 'reject');
        });
    }

    // Gọi các hàm load mỗi 2 giây
    setInterval(loadDeposits, 2000);
    setInterval(loadTransactions, 2000);

    function updateTransaction(id, action) {
        $.ajax({
            url: 'update_transaction.php',
            method: 'POST',
            data: { id: id, action: action },
            success: function(response) {
                const res = JSON.parse(response);
                if (res.status === 'success') {
                    toastr.success(res.message);
                } else {
                    toastr.error(res.message);
                }
                console.log(res);
                loadTransactions();
            }
        });
    }

    function updateDeposit(id, action) {
        $.ajax({
            url: 'update_deposit.php',
            method: 'POST',
            data: { id: id, action: action },
            success: function(response) {
                const res = JSON.parse(response);
                if (res.status === 'success') {
                    toastr.success(res.message);
                } else {
                    toastr.error(res.message);
                }
                loadDeposits();
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                toastr.error('Có lỗi xảy ra, vui lòng thử lại.');
            }
        });
    }
});