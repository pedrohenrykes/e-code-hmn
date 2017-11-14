function form_validation( fields ) {

    for (var i = 0; fields.length > i; i++) {
        
        $('#' + fields[i] ).closest('.form-line').attr('class', 'form-line focused error');
        $('#' + fields[i] ).closest('.bootstrap-select').attr('class', 'btn-group btn-error bootstrap-select form-control show-tick');
        
        var label = '<label id="name-error" class="error" for="name">Campo é obrigatório.</label>';

        $('#' + fields[i] ).closest('.form-group').append( label );

    }

}

function form_input( fields ) {

    for (var i = 0; fields.length > i; i++) {
       
        $('#' + fields[i] ).parent().addClass('focused');

    }

}



function tformdesign_hide_field(form, field) {
	console.log('chamou');
    $('#'+form+' [name="'+field+'"]').closest('.form-line').hide('fast');
}

function tformdesign_show_field(form, field) {
    $('#'+form+' [name="'+field+'"]').closest('.form-line').show('fast');
}
