function incoming_messages_listener() {
        $.get("http://localhost/SNN/ajax/"+user_id+"/messages/"+last_rec_mes_id, 
            function(data, status){
                if (data) {
                    var messages = JSON.parse(data);
                    var last_index = messages.length;
                    last_rec_mes_id = parseInt(messages[last_index-1].message_id);
                    register_last_mes_ts();
                    messages.forEach(display_message);
                    messages.forEach(
                        function (item) {
                            var id = parseInt(item.sender_id);
                            register_chat_unread(id);
                        }
                    );
                    if (chats) {
                        form_chats_bar();
                    }
                }
                if (status=='success') {
                    setTimeout(incoming_messages_listener,1000);
                }
            }
        );
    }