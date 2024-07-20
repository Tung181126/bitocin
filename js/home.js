document.addEventListener('DOMContentLoaded', function() {
    const attendanceIcon = document.getElementById('attendanceIcon');
    const attendanceModal = new bootstrap.Modal(document.getElementById('attendanceModal'));

    attendanceIcon.addEventListener('click', function() {
        attendanceModal.show();
    });

    const days = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
    const today = new Date().getDay();
    const todayId = days[today];

    // Disable all buttons initially
    days.forEach(day => {
        const button = document.getElementById(day);
        if (button) {
            button.disabled = true;
            button.classList.add('btn-secondary');
            button.classList.remove('btn-success');
        }
    });

    // Enable today's button if not checked in
    const todayButton = document.getElementById(todayId);
    if (todayButton && !checkIns.includes(todayId)) {
        todayButton.disabled = false;
        todayButton.classList.add('btn-success');
        todayButton.classList.remove('btn-secondary');
    }

    // Mark checked-in days
    checkIns.forEach(day => {
        const button = document.getElementById(day);
        if (button) {
            button.disabled = true;
            button.classList.add('btn-secondary');
            button.classList.remove('btn-success');
        }
    });

    $('.check-in-btn').on('click', function() {
        var day = $(this).data('day');
        $.ajax({
            url: 'api/check_in.php',
            type: 'POST',
            data: { day: day },
            success: function(response) {
                // Ensure response is parsed as JSON
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }

                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Check-in successful',
                        text: response.message
                    }).then(() => {
                        location.reload(); // Reload the page to update the check-in status
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Check-in failed',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Check-in failed',
                    text: 'An error occurred while processing your request.'
                });
            }
        });
    });

    // Add click event to invite friends icon
    const inviteFriendsIcon = document.getElementById('inviteFriends');
    inviteFriendsIcon.addEventListener('click', function() {
        window.location.href = 'invite_friends.php'; // Change to your invite friends page URL
    });
});

window.onload = function() {
    const days = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
    const today = new Date().getDay();
    const todayId = days[today];
    
    document.getElementById(todayId).disabled = false;
}