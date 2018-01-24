
gb = {
	
	endpoint: '/api/',
	token: null,
	user: {},
	
	get: function(path, callback) {
		return gb.ajax(path, null, callback);
	},
	
	post: function(path, data, callback) {
		return gb.ajax(path, data, callback);
	},
	
	ajax: function(path, data, callback) {
		var options = {
			url: gb.endpoint + path,
			success: callback
		};
		if (gb.token) {
			options.headers = {
				'Authorization': 'Bearer ' + gb.token
			};
		}
		if (data) {
			options.data = data;
			options.type = 'post';
		}
		return $.ajax(options);
	},
	
	auth: function(user) {
		gb.user = user;
		gb.token = gb.user.api_token;
		$('#user-name').text(gb.user.name);
		$('.auth').show();
		$('.not-auth').hide();
		gb.messages();
		
        var channel = gb.pusher.subscribe('answer.' + gb.user.id);
        channel.bind('new-answer', function(data) {
            var elem = $('<div class="alert alert-primary" role="alert">')
                .append("New answer for")
                .append('<br>')
                .append($('<span>').text(data.message.message))
                .append('<br>')
                .append($('<b>').text(data.answer))
            ;
            $.notify({
                title: elem
            }, {
                style: 'basic',
                autoHideDelay: 15000,
                position: 'bottom right'
            });
            console.log(data);
            gb.messages();
        });
	},
	
	login: function() {
		gb.submitForm($('.login-form'), 'login', function(data) {
			gb.auth(data.data);
		});
	},	
	
	message: function() {
		gb.submitForm($('.message-form'), 'messages', function(data) {
			console.log(data);
			$('#message-modal').modal('hide');
			gb.messages();
		});
	},	
	
	answer: function(id) {
		gb.submitForm($('.answer-form'), 'messages/' + id + '/answer', function(data) {
			console.log(data);
			$('#answer-modal').modal('hide');
			gb.messages();
		});
	},	
	
	logout: function() {
		gb.get('logout', function(data) {
			gb.pusher.unsubscribe('answer.' + gb.user.id);			
			gb.token = null;
			gb.user = {};
			$('.auth').hide();
			$('.not-auth').show();
		});
	},	

	messages: function() {
		var page = $('#messages-page').val();
		var perPage = $('#messages-per-page').val();
		console.log(gb.user);
		gb.get('messages/paged/' + page + '/' + perPage, function(data) {
			console.log(data);
			var table = $('<table class="table">');
			for (var i in data) {
				table.append(
					$('<tr>')
						.append($('<td class="p-3" valign="top">').html('#' + data[i].id))
						.append($('<td class="p-3" valign="top">').html(data[i].user_name))
						.append($('<td class="p-3" valign="top" nowrap>').html(data[i].created_at))
						.append($('<td class="p-3">')
							.append($('<p>')
								.text(data[i].message)
								.append(data[i].answer || !gb.user.is_admin ? '' : '<br />')
								.append(
									data[i].answer || !gb.user.is_admin
									? ''
									: $('<a>')
										.attr('href', '#')
										.data('id', data[i].id)
										.click(function() {
											$('.answer-form').data('message_id', $(this).data('id'));
											$('#answer-modal').modal('show');
										})
										.html('Answer')
								)
							)
							.append(
								data[i].answer
								? $('<p>').css('color', '#2020A0').html(data[i].answer)
								: ''
							)
						)
				);
			}
			$('#messages').html('');
			table.appendTo($('#messages'));
		});
	},
	
	submitForm: function(form, path, success) {
		var data = {};
		form.find('input').add(form.find('textarea')).each(function() {
			var val = $(this).val();
			if ($(this).attr('type') == 'checkbox') {
				val = $(this).is(':checked') ? 1 : 0;
			}
			data[$(this).attr('name')] = val;
		});
		return gb.post(
			path,
			data,
			success
		).fail(function(data) {
    		alert(data.responseJSON.message);
  		});
	},
	
	register: function() {
		gb.submitForm($('.register-form'), 'register', function(data) {
			console.log(data);
			$('#register-modal').modal('hide');
			gb.auth(data.data);
		});
	},	

}

$(function() {
	$('.auth').hide();
});