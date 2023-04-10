const populateUsers = (users) => {
    const usersSelect = document.getElementById('users');
    console.log(users);
    users.map((user) => {
        var option = document.createElement('option');
        option.innerText = user.NAME;
        option.value = user.NAME;

        // If the selected meeting has guests, mark the option as selected
        if (selectedMeeting && selectedMeeting.guests.includes(user.NAME)) {
            option.selected = true;
        }

        usersSelect.appendChild(option); // Append option to select
    });

    // Initialize Select2
    $('#users').select2({
        theme: "bootstrap-5",
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
        closeOnSelect: false,
        allowClear: true
    });
};
