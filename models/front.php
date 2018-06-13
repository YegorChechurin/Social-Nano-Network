<!DOCTYPE html>
<html>
<head>
	<title>Front</title>
	<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
</head>
<body>
	
</body>
    <script type="text/javascript">
    	var c;
    	var source = new EventSource("request_handler.php");
    	source.onmessage = function(event) {
    		    c = JSON.parse(event.data);
                $("body").append('<br>Our communication method is '+c);
                //source.close();
            };

    	$(document).ready(
            
        );

    	function SSE() {
    		if(typeof(EventSource) !== "undefined") {
    			communication_type = 'SSE';
    			ajax_sender(communication_type);
    		}
    	}

    	function ajax_sender(info) {
    		$.get('request_handler.php',{communication_type:info},
    			function(data,status) {
    				if (data) {
    					$("body").html('Our communication method is '+data);
    				}
    			});
    	}
    </script>
</html>