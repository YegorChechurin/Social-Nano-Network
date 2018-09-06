class ChatHeader {
    constructor(chat) {
        this.id = chat.partner_id;
        this.html_id = 'c'+this.id;
        this.parsed_id = parseInt(chat.partner_id);
        this.partner_name = chat.partner_name;
        this.last_mes_auth_name = chat.last_mes_auth_name;
        this.last_mes_text = chat.last_mes_text;
        this.ts = Date.parse(chat.last_mes_ts);
    }
    do_css_skeleton() {
        $("#chats_wrapper").append('<div id="'+this.html_id+'"></div>');
        $("#"+this.html_id).html('<b>'+this.partner_name+'</b><br>'+
        this.last_mes_auth_name+': '+this.last_mes_text);
    }
}
