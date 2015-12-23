//参数说明：num 要格式化的数字 n 保留小数位
function formatNum(num,n)
{
    num = String(num.toFixed(n));
    var re = /(-?\d+)(\d{3})/;
    while(re.test(num)) num = num.replace(re,"$1,$2")
    return num;
}

//将数字内容装换为人民币表示
function money_format(num) {
	var num = formatNum(parseFloat(num), 0);

	return "￥ "+num;
}

//标记必填框
function mark_blank_input()
{
  $('.required').each(function(){
    if ($(this).val() == "") {
      $(this).addClass('mark_input');
    }
  });
}
$('.required').blur( function() {
  if ( $(this).val() != '' ) {
    $(this).removeClass('mark_input');
  }
});
//取消标记必填框
$('.required').blur( function() {
  if ( $(this).val() == '' ) {
    $(this).addClass('mark_input');
  }
});