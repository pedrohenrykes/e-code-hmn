function create_icon_model(thema)
{   
    var file_model = 'app/lib/include/tdialogicon/theme3/modal.html';
    var file_icon = 'app/lib/include/tdialogicon/theme3/icon.html';

    readTextFile( file_model, function(argument) {

        $('#adianti_div_content').append(argument);         
    
    })

    readTextFile( file_icon, function(argument) {

        $('.modal-body').html(argument); 

        $('#largeModal').modal();  
                                
        $('.modal-body').delegate( '#icon_action', 'click', function() {                       
               
            $('#icon_value').val( 'fa:'+  this.querySelector('i').className.replace('fa fa-',''));
            $('#icon_a').html('<i style="top: 5px;" id="icon_image" class="'+ this.querySelector('i').className +'"></i>');

        });    
        
    })

}

function readTextFile(file, callback) {

    var rawFile = new XMLHttpRequest();
    rawFile.open("GET", file, false);
    rawFile.onreadystatechange = function ()
    {
        if(rawFile.readyState === 4)
        {
            if(rawFile.status === 200 || rawFile.status == 0)
            {                                

                var allText = rawFile.responseText;                    

                callback(allText);

            }
        }
    }

    rawFile.send(null);

}

function loadImage(value){

    $('#icon_a').html('<i style="top: 5px;" id="icon_image" class="fa fa-'+  value.replace('fa:', '') +'"></i>');
}


function myFunction() {

    var input, filter, ul, li, a, i;
    input = document.getElementById('myInput');
    filter = input.value.toUpperCase();

    ul = document.getElementById("fa-icons");
    li = ul.getElementsByClassName('icon_action');

    for (i = 0; i < li.length; i++) {
        
        //if (a.innerHTML.toUpperCase().indexOf(filter) > -1) {
        if (li[i].textContent.toUpperCase().indexOf(filter) > -1) {            

            console.log( li[i].textContent );

            li[i].style.display = "";

            $( li[i] ).closest().css('display', '');

        } else {

            $( li[i] ).closest().css('display', 'none');

            li[i].style.display = "none";
        }
    }
}