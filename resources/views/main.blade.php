<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>{{ config('app.name') }}</title>
    <style>
        .auth {
            display: none;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">{{ config('app.name') }}</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="#">Home</a>
                </li>
                <li class="nav-item not-auth">
                    <a class="nav-link" href="#" data-toggle="modal" data-target="#register-modal">Register</a>
                </li>
                <li class="nav-item auth">
                    <a class="nav-link" href="#" data-toggle="modal" data-target="#message-modal">Post Message</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item auth">
                    <span class="nav-link">Welcome,&nbsp;<b id="user-name">User</b></span>
                </li>
                <li class="nav-item auth">
                    <a class="nav-link" href="#" onclick="gb.logout()">Logout</a>
                </li>
            </ul>
            <form class="form-inline not-auth login-form">
                <input class="form-control mr-sm-2" type="text" name="email" placeholder="Email" aria-label="Email">
                <input class="form-control mr-sm-2" type="password" name="password" placeholder="Password" aria-label="Password">
                <button class="btn my-2 my-sm-0" type="button" onclick="gb.login()">Login</button>
            </form>
        </div>
    </nav>

    <main class="container">
            <div class="p-3">
                <form class="form-inline paging-form">
                    <div class="form-group">
                    <label>Page</label>
                    <input class="form-control mx-sm-2" type="text" id="messages-page" name="page" placeholder="Page" value="1">
                    <label>Per Page</label>
                    <input class="form-control mx-sm-2" type="text" id="messages-per-page" name="per-page" placeholder="Per-Page" value="5">
                    <button class="btn my-2 my-sm-0" type="button" onclick="gb.messages()">Show Messages</button>
                </form>
            </div>
        <div class="p-3" id="messages">
        </div>
    </main>


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

    <script src="{{ URL::asset('js/main.js') }}"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

    <script src="{{ URL::asset('js/notify.min.js') }}"></script>

    <script src="https://js.pusher.com/4.0/pusher.min.js"></script>
    <script>
        
        $(function() {
        
            $.notify.addStyle('basic', {
                html: "<div><div class='clearfix'><div class='title' data-notify-html='title'/></div></div>"
            });
            
            // Enable pusher logging - don't include this in production
            Pusher.logToConsole = true;

            gb.pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                cluster: 'eu',
                encrypted: true
            });

            var channel = gb.pusher.subscribe('messages');
            channel.bind('new-message', function(data) {
                var elem = $('<div class="alert alert-secondary" role="alert">')
                    .append("New message from ")
                    .append($('<b>').text(data.user.name))
                    .append(':')
                    .append('<br>')
                    .append($('<span>').text(data.message.message))
                ;
                $.notify({
                    title: elem
                }, {
                    style: 'basic',
                    autoHideDelay: 10000,
                    position: 'bottom right'
                });
                gb.messages();
                console.log(data);
            });
            
        });

    </script>
    
    <div class="modal fade" id="register-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Register New User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="register-form">
                        <div class="form-group">
                            <input type="text" name="name" class="form-control register-form-input" placeholder="Name">
                        </div>
                        <div class="form-group">
                            <input type="text" name="email" class="form-control register-form-input" placeholder="Email">
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" class="form-control register-form-input" placeholder="Password">
                        </div>
                        <div class="form-group">
                            <input type="password" name="password_confirmation" class="form-control register-form-input" placeholder="Confirm password">
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" name="is_admin" type="checkbox" value="1">
                                <label class="form-check-label">
                                  Is Admin
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="gb.register()">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="message-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Post Message</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="message-form">
                        <div class="form-group">
                            <textarea class="form-control" name="message" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="gb.message()">Submit</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="answer-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Post Answer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="answer-form">
                        <div class="form-group">
                            <textarea class="form-control" name="message" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="gb.answer($('.answer-form').data('message_id'))">Submit</button>
                </div>
            </div>
        </div>
    </div>
    
</body>

</html>