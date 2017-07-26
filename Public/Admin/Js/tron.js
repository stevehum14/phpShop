/**
 * Created by stevehum on 17/7/25.
 */
$(".tron").mouseover(function(){
    $(this).find("td").css("backgroundColor",'#a7bbbf');
})
$(".tron").mouseout(function(){
    $(this).find("td").css("backgroundColor",'#FFF');
})