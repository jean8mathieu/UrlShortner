<?php
/**
 * Created by PhpStorm.
 * User: Jean-Mathieu
 * Date: 3/1/2016
 * Time: 7:58 PM
 */


include('API.php');
if(isset($_GET['l']) && strlen($_GET['l']) > 0){
    $api = new API();
    $url = json_decode($api->getURL($_GET['l']));

    $final = $url->result[0]->url_normal;
    
    if($url->error == "true"){
        echo "We couldn't find the appropriate url for you. Make sure this is a valid code.";
    }else {
        if (strpos($final, 'http') !== FALSE) {
            header('location: ' . $final);
        } else {
            header('location: http://' . $final);
        }
    }
}else{
    ?>
    <html>
    <head>
        <title>URL Shortner</title>
        <script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
        <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

        <link rel="stylesheet" href="css/main.css">
		
		<link rel="icon" href="img/favicon.ico" type="image/x-icon"/>
	<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon"/>
    </head>
    <body>
        <div class="container">
            <h1 style="text-align: center">Generate your unique short URL!</h1>
            <div class="row">
                <div class="col-md-10">
                    <input type="url" class="form-control" id="url">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-default" style="width: 100%" id="generate">Generate It!</button>
                </div>

            </div>

            <div class="row">
                <div class="col-md-12" style="padding-top: 20px">
                    <table class="table table-hover" id="generatedTable" style="max-width: 100%">
                    </table>
                </div>
            </div>
        </div>

    <script>
        $(document).ready(function(){
            generateTable();


        });

        setInterval(function(){
            $('#generatedTable').fadeOut('slow');
            setTimeout(function(){
                generateTable();
                $('#generatedTable').fadeIn('slow');
            },1000);
        }, 60000);

        $('#generate').click(function(){
            if($('#url').val().length > 0){
                $.ajax({
                    type: "GET",
                    url: "algo.php/?method=insert&url=" + encodeURIComponent($('#url').val()),
                    success: function(data){
                        if(data.error == "false"){
                            var url = "http://jmdev.ca/url/?l=" + data.result.url_short;
                            $('#short').html("Your URL: <a href='" + url + "' target='_blank'>"+ url +"</a>");
                            $('#popup').modal('show');
                            generateTable();
                        }else{
                            alert("We couldn't generate the URL..." + JSON.stringify(data));
                        }
                    },
                    error: function(){
                        alert("ERROR: We couldn't generate the url...");
                    }
                });
            }else{
                alert("URL can't be empty");
            }
        });

        function generateTable(){
            $('#generatedTable').html('<div id="loader" style="top: 100px"></div>');
            $.ajax({
                type: "GET",
                url: "algo.php?method=read&limit=50",
                success: function(data){
                    $('#generatedTable').html('');
                    $('#generatedTable').append('<tr><th>ID</th><th>Normal URL</th><th>Short URL</th><th>Views</th></tr>');
                    for(var i = 0; i < data.result.length; i++){
                        var url_normal = data.result[i].url_normal;
                        var url_short =  "http://jmdev.ca/url?l=" + data.result[i].url_short;

                        $('#generatedTable').append('<tr><td>' + data.result[i].url_id + '</td><td style="word-break: break-all;"><a href="' + url_normal  + '" target="_blank">' + url_normal  + "</td><td><a href='" + url_short + "' target='_blank'>" + url_short + "</td><td style='text-align: center'>" + data.result[i].views + "</td></tr>");
                    }
                },
                error: function(){
                    alert("Can't get data");
                }
            });
        }
    </script>
        <div class="modal fade" id="popup" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Your Generated URL</h4>
                    </div>
                    <div class="modal-body">
                        <p id="short"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </body>
    </html>



<?php
    //echo json_encode(array("error" => 'true', 'result' => array('This is an API')));
}
?>
