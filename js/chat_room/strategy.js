event source -> event listener -> event handler

event listener -> event handler -> callbacks

Someone fires event and notifies about it 
the one who should be notified. The notified one knows what functions
should be called in case of this event. So it calls 
all the right functions.

message -> chat=collection_of_messages -> chats

chat has a chat_header

message can be sent and received

chat_header is a subject, when it is clicked it changes its state - 
its class becomes to be "active_chat_header", corresponding observer 
gets notified about it and does all the necessary job 
(display messages, marks chat as read)

