@extends('layout.master')

@section('main_content')

<div class="container">
    {{-- <h2>Learning Centre Management System</h2> --}}
    <h4>Student Details</h4>
    <form id="student-info">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="dob">Date of Birth</label>
            <input type="date" class="form-control" id="dob" name="dob" required>
        </div>
        <div class="form-group">
            <label for="Status">Enrollment Status</label>
            <input type="text" class="form-control" id="status" name="status" required>
        </div>
        <div class="form-group">
            <label for="time_slot">Choose your time slot</label>
            <select class="form-control" id="time_slot" name="time_slot" required>
                <option value="">Choose a time slot</option>
                <option value="Saturday, 8pm">Saturday, 8pm</option>
                <option value="Friday, 3pm">Friday, 3pm</option>
                <option value="Wednesday, 10am">Wednesday, 10am</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Confirm Time</button>
    </form>

    <div id="schedule-container" class="mt-4 container" style="display: none;">
        <h4>Schedule for <span id="time-slot-display"></span></h4>
        <!-- Add Button -->
        <!-- <button id="add-task-btn" class="m-2 btn btn-primary">Add Task</button> -->
        <table id="schedule-table" class="display" style="width: 100%;">
            <thead>
                <tr>
                    {{-- <th>ID</th> --}}
                    <th>Schedule Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Has attended?</th>
                    <th></th>
                    {{-- <th>Created At</th>
                    <th>Updated At</th> --}}
                </tr>
            </thead>
        </table>
    </div>

</div>

<script>
    $(document).ready(function () {
        var studentId = null;
        var time_slot = null;
        var schedule_id = null;

        $.ajax({
            url: '{{ route('student.data') }}',
            method: 'GET',
            success: function(data) {
                // Populate form fields with the fetched data
                $('#name').val(data.first_name);
                $('#dob').val(data.dob);
                $('#status').val(data.status);
                // $('#time_slot').val(data.time_slot);
                studentId=data.id;
            },
            error: function(xhr) {
                console.log('Error fetching student data:', xhr.responseText);
            }
        });

        $('#student-info').on('submit', function (e) {
            e.preventDefault();

            // Manually get form values
            time_slot = $('#time_slot').val();
            console.log('time slot chosen '+ time_slot)

            // Split the string by comma
            var parts = time_slot.split(',');

            // Extract the day and time
            var dayOfWeek = parts[0].trim();
            var timeOfDay = parts[1].trim();

            // Convert time from '10am' to '10:00:00'
            var timeFormat = timeOfDay.toLowerCase();
            var hours = parseInt(timeFormat);
            var period = timeFormat.includes('pm') ? 'PM' : 'AM';
            if (period === 'PM' && hours !== 12) {
                hours += 12;
            } else if (period === 'AM' && hours === 12) {
                hours = 0;
            }
            var formattedTime = ('0' + hours).slice(-2) + ':00:00';

            // Now `dayOfWeek` is 'Wednesday' and `formattedTime` is '10:00:00'
            console.log("Student Id: "+studentId);
            console.log(dayOfWeek); // 'Wednesday'
            console.log(formattedTime); // '10:00:00'

             // Create a data object
             var dataObject = {
                dayOfWeek: dayOfWeek,
                formattedTime: formattedTime,
                studentId: studentId
            };

            $.ajax({
                url: 'api/generate-schedule',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(dataObject),
                success: function (response) {
                    scheduleTable.clear().draw(); // Clear and redraw the DataTable
                    $('#time-slot-display').text(time_slot);
                    // Show the schedule container
                    $('#schedule-container').show();
                },
                error: function (xhr) {
                    console.error('An error occurred:', xhr.responseText);
                }
            });

        });

        var scheduleTable = 
        $('#schedule-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('schedule.data') }}',
            columns: [
                // { data: 'id', name: 'id' },
                { data: 'lesson_date', name: 'lesson_date' },
                { data: 'lesson_start_time', name: 'lesson_start_time' },
                { data: 'lesson_end_time', name: 'lesson_end_time' },
                {
                    data: 'has_attended',
                    name: 'has_attended',
                    render: function (data, type, row) {
                        // Format boolean values
                        return data === 1 ? 'Yes' : 'No';
                    }
                },
                {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return `
                        <button class="btn btn-sm btn-primary edit-btn" data-id="${row.id}">
                            Click to attend
                        </button>
                    `;
                    }
                }
                // { data: 'created_at', name: 'created_at' },
                // { data: 'updated_at', name: 'updated_at' }
            ]
        });

        $('#schedule-table').on('click', '.edit-btn', function() {
            var button = $(this);
            var row = button.closest('tr'); // Get the closest row
            schedule_id = button.data('id'); // Get the ID or any other data attribute
            var rowData = scheduleTable.row(row).data();
            
            // Perform an action with the ID
            console.log('Button clicked with ID:', schedule_id);
            console.log(rowData);

            // Modify a field in rowData
            rowData.has_attended = 1;

            console.log(rowData);

            $.ajax({
                url: '/api/schedule/'+schedule_id,
                method: 'PUT',
                contentType: 'application/json',
                data: JSON.stringify(rowData),
                success: function (response) {
                    scheduleTable.clear().draw(); // Clear and redraw the DataTable
                },
                error: function (xhr) {
                    console.error('An error occurred:', xhr.responseText);
                }
            });
    });

    });  
</script>
@endsection