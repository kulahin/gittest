$(function() {
    $( "#datepicker" ).datepicker({
        dateFormat: 'yy-mm-dd',
        autoSize: true,
        onSelect: function() {
            $('#date h2').text($(this).attr('value'));
            get_list($(this).attr('value'));
        }
    });
});

function get_list(date){
    $.get('list.php?date='+date, function(data) {
        $('#rating').html(data);
    });
}