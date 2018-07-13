$(function() {
 $('img.linktip').wrap('<span class="tip" />'); //оборачиваем соответствующие элементы в контейнер
  $('span.tip').each(function(){
       myTip = $(this),
       tipLink = myTip.children('img'),
       tBlock = myTip.children('span').length, //подсчитываем дочерние SPAN элементы внутри контейнера
       tTitle = tipLink.attr('title') != 0, //определяем наличие тега TITLE
       tipText = tipLink.attr('title'); //берем текст из тега TITLE

     tipLink.removeAttr("title"); //скрываем обычный TITLE
   //условие - если внутри нет доч. SPAN и есть TITLE,
   //добавляем соответствующий SPAN с текстом взятым из TITLE
     if(tBlock === 0 && tTitle === true){myTip.append('<span class="answer">' + tipText + '</span>')};

     var tip = myTip.find('span.answer , span.answer-left').hide(); //найдем и скроем блоки с подсказками

//при наличии у ссылки тега EM подсказка будет появляется по клику
//также сразу добавим и "крестик" закрытия
     tipLink.has('em').click(showTip).siblings('span').append('<b class="close">X</b>');

//если тага EM нет, подсказка будет появляться при наведении курсора
	tipLink.not($('em').parent()).hoverIntent(
       showTip,
     function(){
       tip.fadeOut(200);}
    );
//закрытие подсказки при клике на "крестик"
    tip.on('click', '.close', function(){
       tip.fadeOut(200);}
    );

//функция вывода и появления подсказки на экран
//вне зависимости от размеров окна,
//наличия горизонтальной или вертикальной прокрутки
//подсказка всегда будет в видимой области
    function showTip(e){
       xM = e.pageX,
       yM = e.pageY,
       tipW = tip.width(),
       tipH = tip.height(),
       winW = $(window).width(),
       winH = $(window).height(),
       scrollwinH = $(window).scrollTop(),
       scrollwinW = $(window).scrollLeft(),
       curwinH = $(window).scrollTop() + $(window).height();
    if ( xM > scrollwinW + tipW * 2 ) {tip.removeClass('answer').addClass('answer-left');}
       else {tip.removeClass('answer-left').addClass('answer');}
    if ( yM > scrollwinH + tipH && yM > curwinH / 2 ) {tip.addClass('a-top');}
       else {tip.removeClass('a-top');}
    tip.fadeIn(100).css('display','block');
   e.preventDefault();
   };
 });
});/*конец*/