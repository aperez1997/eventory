function scrollToPosition(element) {
  if (element !== undefined) {
    $('html, body').animate({
        scrollTop: $(element).offset().top - 40
    }, 200);
   }
}
$(function() {
    var posts = $('.entry');
    var position = 0;

var isEnabled = true;
var enable = function(){
	isEnabled = true;
};
var wait = 250;
    var nextEntry = function() {
        if (position >= posts.length - 1 || !isEnabled){
		return false;
	}
	isEnabled = false;
	setTimeout(enable, wait);
        scrollToPosition(posts[position += 1]);
    };
    var prevEntry = function() {   
        if (position == 0 || !isEnabled){
		return false;
	}
	isEnabled = false;
	setTimeout(enable, wait);
	scrollToPosition(posts[position -= 1]);
    };
    $(document).keydown(function(e) {
        if (e.which == 32 || e.which == 34) {
            nextEntry();
		return false;
        } else if ( e.which == 33) {
            prevEntry();
		return false
        } else if (e.keyCode == 36){
		position = 0;
	} else if (e.keyCode == 35){
		position = posts.length - 1;
	}
    });
});
