/* エンターキーでの'submit'を禁止する */
$(function(){
    $("input"). keydown(function(e) {
        if ((e.which && e.which === 13) || (e.keyCode && e.keyCode === 13)) {
	    return false;
        } else {
            return true;
        }
    });
});


/* validationEngineをform_1に適用 */
jQuery(function(){
    jQuery("#form_1").validationEngine();
});
jQuery(function(){
    jQuery("#form_2").validationEngine();
});


/* パスワード生成ツール */
function getPassword() {
    var len = 16;
    var seed0 = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var seed = '';
    seed =  seed0;
    var pwd = '';
    while (len--) {
	pwd += seed0[Math.floor(Math.random() * seed.length)];
    }
    $('#result').val(pwd);
}

