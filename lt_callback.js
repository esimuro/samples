
LibraryThingConnector_ReviewsOnLoad = function() {
    var headers = new Array();
    headers[0] = '0 reviews';
    headers[1] = 'Add review';
    var fndStr = '/vufind/';
    var url = window.location.href;
    var p = url.indexOf(fndStr);
    url = url.slice(0,p);
    var resultA = $('.result');
    if( resultA.length > 0 ) {
        for( var i = 1; i < resultA.length; i +=2) {
            // id=record137959
            var recordId = $(resultA[i]).attr('id').split('record')[1];
            var newRevLinkA = url + '/vufind/Record/' + recordId + '#LT_reviews=';
            var reviewBase = $(resultA[i]).find('.ltfl_reviews');
            var currLink = $(resultA[i]).find('.ltfl_reviews a');
            $(currLink).attr('target', '_self');
            $(currLink).removeAttr('onclick'); 
            var spans = $(reviewBase).find('span');
            if( currLink.length > 0 ) {
                $(currLink).attr('href', newRevLinkA);
            }
            var textLine = $(currLink).text();
            var noReview = false;
            for (h = 0; h < headers.length; h++ ) {
                if ($.trim(textLine) == headers[h]) {
                    var newHtml = '<span class="ltfl_review_text">Add review</span>';
                    if (spans.length > 0 ) {
                        for (var sp = 0; sp < spans.length; sp++) {
                            $(spans[sp]).html('');
                        }
                    }
                    $(currLink).html(newHtml);
                    noReview = true;
                }
            } 
            if (! noReview ) {
                if (spans.length > 0 ) {
                    for (var sp = 0; sp < spans.length; sp++) {
                        if ( sp  == 0 ) {
                            continue;
                        }
                        if ($.trim(spans[sp].textContent) == '0 Reviews') {
                            newHtml = 'Add review';
                            $(spans[sp]).html(newHtml);
                            continue;
                        }
                        var sj = sp + 1;
                        var newHtml = '';
                        var myHtml = $(spans[sj]).html();
                        var repStr1 = ';&nbsp;';
                        var repStr2 = '(&nbsp;'
                        var repStr3 ='&nbsp;)';
                        var p1 = myHtml.indexOf(repStr1);
                        var p2 = p1 + repStr1.length;
                        var p3 = myHtml.indexOf(repStr2, p1);
                        var p4 = myHtml.indexOf(repStr3, p3);
                        newHtml = myHtml.slice(0, p1);
                        if (p3 == -1 ) {
                            newHtml += myHtml.slice(p2);
                        } else {
                            newHtml += myHtml.slice(p2, p3);
                            newHtml += '<br />';
                            p3 += repStr2.length;
                            newHtml += myHtml.slice(p3, p4);
                        }
                        $(spans[sp]).html(newHtml);
                        $(spans[sj]).html('');
                        break;
                    }
                } 
            }
        }
    }
}


