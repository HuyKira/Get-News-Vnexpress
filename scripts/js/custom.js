(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v2.7";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

$(document).ready(function() {
    var html = '<div class="form-group">';
        html +='<label for="link">Nhập link bài viết</label>';
        html +='<input required="required" name="link[]" type="url" class="form-control" id="" placeholder="Nhập link vào đây">';
        html +='<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>';
        html +='</div>';
    $('.click-more').click(function() {
        $('.list-input').append(html);
    });
    $('form').on('click', '.glyphicon-remove', function(event) {
        $(this).parent('.form-group').remove();
    });
    var html2 = '<div class="col-xs-12 col-sm-12 col-md-6"><div class="input-hk form-group"><input type="text" class="form-control" name="add_menu_hk[list-op][]" value=""><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></div></div>';
    $('.click-add').click(function() {
        $('.list-hihi').append(html2);
    });
});