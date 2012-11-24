$(".confirm").live("click", function (event) {
    var data = event.target.id.split('-');
    var url = data[5] + '-' + data[6];
    var title = data[4];

    $.ajax({
        type: "POST",
        url: url,
        data: { team_id: data[0], player_id: data[1], season: data[2], match_round: data[3], response: 'no' },
        dataType: "json",
        success: function(jsonData){
            var text = "<div>" +
                /*title + " ha entrenado " + jsonData.new_minutes_trained + " minutos al " +
                jsonData.new_percentage_trained + "% de intensidad en el último partido. ¿Quieres guardar este valor?<br><br>" +
                "Recuerda verificar este valor porque puede no ser correcto. Debes recargar la página para ver la celda con color." +
                "<br><br><hr><br><br>" +*/
                title + " has trained " + jsonData.new_minutes_trained + " minutes at " +
                jsonData.new_percentage_trained + "% training intensity in the last match. ¿Do you want to save this value?<br><br>" +
                "Remember to verify this value because it could be wrong. You must reload the page to see the cell colored." +
                "</div>";
            yes_no_dialog(event, title, url, data, text, 'Save', 'Discard', 'Skip', $(this));
        }
    });
});

function yes_no_dialog(event, title, url, data, text, yesButton, noButton, laterButton, element){
    var buttons = {};
    buttons[yesButton] = function(){
        $.ajax({
            type: "POST",
            url: url,
            data: { response: "1", data: data },
            dataType: "json"
        });
        $(this).dialog("close");
        $(event.target).hide();
    };
    buttons[noButton] = function(){
        $.ajax({
            type: "POST",
            url: url,
            data: { response: "0", data: data },
            dataType: "json"
        });
        $(this).dialog("close");
        $(event.target).hide();
    };
    buttons[laterButton] = function(){
        $(this).dialog("close");
    };

    $(text).dialog({
        autoOpen: true,
        title: title,
        modal:true,
        buttons:buttons
    });
}