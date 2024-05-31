$(".tcm-search-filter-name-provider > legend > span").text('Category');
function view_description(clickedDiv){
    $(document).find('.tcm-details-description').remove();
    var learningObjectivesValue = $(clickedDiv).find('.learning_objectives').text();
    if ($('.tcm-details-description').length > 0) {
    appendPTag(learningObjectivesValue);
    } else {
    var checkClassInterval = setInterval(function() {
        if ($('.tcm-details-description').length > 0) {
        
        clearInterval(checkClassInterval);
        appendPTag(learningObjectivesValue);
        }
    }, 100); // Adjust the interval as needed
    } 
}
function appendPTag(learningObjectivesValue) {
    $('.tcm-details-description').append("<h4>Learning Objectives </h4>");
$('.tcm-details-description').append(learningObjectivesValue);
}
