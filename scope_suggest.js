
function suggest(inputString, e){
    var code = e.which;
    switch(code) {
        // for not the only code here is Esc latter we can add the other keys and functions
        case 27:
            getout();
            return;
            break;   
    }
    if(inputString.length == 0) {
        $('#suggestions').fadeOut();
    } else if (inputString.length < 2 ) {
        // do nothing until they enter two characters
    } else {
        var now = new Date();
        var ts = Date.UTC(now.getFullYear(),now.getMonth(),now.getDay(),now.getHours(),now.getMinutes(),now.getSeconds(),now.getMilliseconds());
        myData = "namePart=" + encodeURIComponent(inputString);
        myData += "&time="+ts;
        $('#searchFor').addClass('load');
        $.ajax({
            dataType: 'json',
            url: path + '/AJAX/JSON?method=getScopeSuggestions',
            data: myData,
            success: function(response) {
                if(response.status == 'OK') {
                    var items = response.data;
                    if(items.length >0) {
                        $('#suggestionsList').html(items);
                        var displayItem = document.getElementById('suggestions');  
                        displayItem.style.display = '';
                     //   $('#suggestions').fadeIn();
                        $('#country').removeClass('load');
                    } else {
                        getout(); 
                    }
                }
            }
        });
    }
}

function fill(thisValue) {
    $('#searchForEntry').val(thisValue);
    setTimeout("$('#suggestions').fadeOut();", 400);
}

function getout() {
    setTimeout("$('#suggestions').fadeOut();", 400);
}

 
function loadScope(value) {
    if (typeof value != 'undefined') {
        var split = value.split("--");
        if (split[0] == 'all' ) {
            $('#searchForEntry').val('All Libraries');
            split[1] = undefined;
        } else {
            $('#searchForEntry').val(split[0]);
        }
        var form = document.getElementById("searchForm");      
        form.scopeChanged.value = '1';
        form.searchFor.value =  split[0];
        form.searchType.value =  split[1];
        $('#searchForEntry').value = split[0];
        changeDisplay = '<p class="scopeLine">'; 
        if (split[1] == 4 ) {
            changeDisplay += '<span>Library Group: </span><span>' + split[0] + '</span></p>';
        } else if (split[1] == 5 ) {
            changeDisplay += '<span>Institution: </span><span>' + split[0] + '</span></p>';
        } else if (split[1] == 6 ) {
            changeDisplay += '<span>Library: </span><span>' + split[0] + '</span></p>';
        } else {
            changeDisplay += '<span>All Libraries </span></p>';
        }
        $('#mScopeArea').html(changeDisplay);
        $('#mScopeArea').addClass('scopeArea scopeAreaMid'); 
    }
    setTimeout("$('#suggestions').fadeOut();", 600);
}

function holdToLightbox( id, title) {
    var len = 'placeHold'.length; 
    var bar = id.indexOf("|");
    var idn = id.substr(len, bar - len);
    var patron =  id.substr( bar + 1);
    var module = 'Record';
    var $dialog = getLightbox('Record', 'Placehold', idn, patron, this.title);
    return false;
}
