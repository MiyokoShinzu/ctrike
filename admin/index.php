<?php include "./globals/head.php"; ?>

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: rgb(174, 14, 14);
            --primary-light: rgb(220, 60, 60);
            --accent: #ff904c;
            --background: #fff7f7;
            --text: #222;
            --border-radius: 12px;
            --box-shadow: 0 2px 12px rgba(174, 14, 14, 0.08);
        }

        body {
            background: var(--background);
            color: var(--text);
            font-family: 'Poppins', sans-serif;
        }

        #main {
            transition: margin-left 0.3s ease, width 0.3s ease;
            margin-left: 250px;
            width: calc(100% - 250px);
        }

        @media (max-width: 991px) {
            #main {
                margin-left: 0;
                width: 100%;
            }
        }

        .card {
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            background: #fff;
            transition: 0.3s;
        }

        h5,
        h6 {
            color: var(--primary);
            font-weight: bold;
        }

        .card-fixed {
            height: 130px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .chart-card {
            height: 280px;
        }

        .text-muted {
            font-size: 0.85rem;
        }
    </style>
</head>

<body>
    <?php include "./globals/navbar.php"; ?>

    <div id="main" class="container-fluid py-4 mt-5 bg-white">

        <div class="row">
            <div class="col-lg-11 mx-auto border-0 border-bottom pb-3 mb-3 d-flex justify-content-between align-items-center">
                <h3>User Accounts</h3>
            </div>
            <div class="col-lg-11 mx-auto">
                <div class="table-responsive">
                    <table class="table table-bordered" id="user_table">
                        <thead>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Contact</th>
                            <th>Address</th>
                            <th>Vehicle ID</th>
                            <th>Access Level</th>
                            </tr>
                        </thead>
                        <tbody id="user_table_data"></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal -->
    <div
        class="modal fade"
        id="add_user_modal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="modalTitleId"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="text-secondary" id="modalTitleId">
                        Add User
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12 mx-auto">
                                <div class="form-floating mb-3">
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="formId1"
                                        id="username"
                                        placeholder="" />
                                    <label for="username">Username</label>
                                </div>

                            </div>
                            <div class="col-md-12 mx-auto">
                                <div class="form-floating mb-3">
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="formId1"
                                        id="fullname"
                                        placeholder="" />
                                    <label for="fullname">Fullname</label>
                                </div>

                            </div>
                            <div class="col-md-12 mx-auto">
                                <div class="form-floating mb-3">
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="formId1"
                                        id="address"
                                        placeholder="" />
                                    <label for="formId1">Complete Address</label>
                                </div>

                            </div>
                            <div class="col-md-12 mx-auto">
                                <div class="form-floating mb-3">
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="formId1"
                                        id="contact"
                                        placeholder="" />
                                    <label for="contact">Contact</label>
                                </div>

                            </div>
                            <div class="col-md-6 mx-auto">
                                <div class="form-floating mb-3">
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="formId1"
                                        id="password"
                                        placeholder="" />
                                    <label for="formId1">Password</label>
                                </div>

                            </div>
                            <div class="col-md-6 mx-auto">
                                <div class="form-floating mb-3">
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="formId1"
                                        id="vehicle_id"
                                        placeholder="" />
                                    <label for="vehicle_id">Vehicle ID</label>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" id="save_btn">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        var modalId = document.getElementById('modalId');

        modalId.addEventListener('show.bs.modal', function(event) {
            // Button that triggered the modal
            let button = event.relatedTarget;
            // Extract info from data-bs-* attributes
            let recipient = button.getAttribute('data-bs-whatever');

            // Use above variables to manipulate the DOM
        });
    </script>

    <?php include "./globals/scripts.php"; ?>

    <script>
        fetch(`../api/admin_select_users.php`)
            .then(res => res.json())
            .then(data => {
                var text = '';
                data.forEach(item => {
                    text += `<tr>
                <td>${item.username}</td>
                <td>${item.fullname}</td>
                <td>${item.contact}</td>
                <td>${item.address}</td>
                <td>${item.vehicle_id}</td>
                <td>${item.access == 0 ? 'Admin' : 'User'}</td>
            </tr>`;
                })
                $('#user_table_data').html(text);
                $('#user_table').DataTable({
                    dom: 'fQrBtip',
                    responsive: true,
                    buttons: [{
                            text: 'Add User',
                            className: 'add_case',
                            attr: {
                                'data-bs-toggle': 'modal',
                                'data-bs-target': '#add_user_modal',
                                'title': 'Click to add user'
                            }
                        },
                        {
                            extend: 'excel',
                            text: 'Excel',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'pdf',
                            text: 'PDF',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'print',
                            text: 'Print',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'colvis',
                            text: 'Show/Hide Columns'
                        }
                    ],
                    fixedHeader: true,
                    paging: true,
                    searching: true,
                    ordering: true,
                    scrollY: '300px',
                    colReorder: true,
                    scrollCollapse: true,
                    language: {
                        search: 'Search:'
                    }
                });
                console.log(data);

            })
            .catch(err => console.error(err))
    </script>
    <script>
        $(document).on('click', '#save_btn', function() {
            let username = $('#username').val();
            let fullname = $('#fullname').val();
            let address = $('#address').val();
            let contact = $('#contact').val();
            let password = $('#password').val();
            let vehicle_id = $('#vehicle_id').val();

            fetch(`../api/admin_insert_account.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        username: username,
                        fullname: fullname,
                        address: address,
                        contact: contact,
                        password: password,
                        vehicle_id: vehicle_id
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Failed: ' + data.message);
                    }
                })
                .catch(err => console.error(err));
        });
    </script>

</body>

</html>