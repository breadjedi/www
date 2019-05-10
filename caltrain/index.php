<html>
<head>
<style type="text/css">
div, table, select {
  font-size : 1.5em;
}

a {
  text-decoration: none;
}

tr {
  vertical-align: top;
}

.hidden {
  display: none;
}

.headerButton {
  position: relative;
  top: -5px;
  cursor: pointer;
  padding: 5px;
  text-align: center;
}
</style>
<style type="text/css" id="hideLeft">
.left { display: none; }
</style>
<style type="text/css" id="hideTop">
.top { display: none; }
</style>

<script type="text/javascript">
<? require( "stops.txt" ); ?>

function setcookie( name, value, expires, path, domain, secure )
{
  // set time, it's in milliseconds
  var today = new Date();
  today.setTime( today.getTime() );

  /*
    if the expires variable is set, make the correct
    expires time, the current script below will set
    it for x number of days, to make it for hours,
    delete * 24, for minutes, delete * 60 * 24
  */
  if ( expires )
  {
    expires = expires * 1000 * 60 * 60 * 24;
  }
  var expires_date = new Date( today.getTime() + (expires) );

  document.cookie = name + "=" +escape( value ) +
    ( ( expires ) ? ";expires=" + expires_date.toGMTString() : "" ) +
    ( ( path ) ? ";path=" + path : "" ) +
    ( ( domain ) ? ";domain=" + domain : "" ) +
    ( ( secure ) ? ";secure" : "" );
}

function getcookie( check_name ) {
  // first we'll split this cookie up into name/value pairs
  // note: document.cookie only returns name=value, not the other components
  var a_all_cookies = document.cookie.split( ';' );
  var a_temp_cookie = '';
  var cookie_name = '';
  var cookie_value = '';
  var b_cookie_found = false; // set boolean t/f default f

  for ( i = 0; i < a_all_cookies.length; i++ )
  {
    // now we'll split apart each name=value pair
    a_temp_cookie = a_all_cookies[i].split( '=' );


    // and trim left/right whitespace while we're at it
    cookie_name = a_temp_cookie[0].replace(/^\s+|\s+$/g, '');

    // if the extracted name matches passed check_name
    if ( cookie_name == check_name )
    {
      b_cookie_found = true;
      // we need to handle case where cookie has no value but exists (no = sign, that is):
      if ( a_temp_cookie.length > 1 )
      {
      	cookie_value = unescape( a_temp_cookie[1].replace(/^\s+|\s+$/g, '') );
      }
      // note that in cases where cookie is initialized but no value, null is returned
      return cookie_value;
      break;
    }
    a_temp_cookie = null;
    cookie_name = '';
  }
  if ( !b_cookie_found )
  {
    return null;
  }
}

function showAllToggle( dir ) {
  if ( dir == 'left-right' ) {
    var hideLeft = document.getElementById('hideLeft');

    if ( hideLeft.sheet.disabled ) {
      hideLeft.sheet.disabled = false;
      document.getElementById('showLeftToggle').innerHTML = '&larr;&rarr;';
    } else {
      hideLeft.sheet.disabled = true;
      document.getElementById('showLeftToggle').innerHTML = '&rarr;&larr;';
    }
  }

  if ( dir == 'top-bottom' ) {
    var hideTop = document.getElementById('hideTop');

    if ( hideTop.sheet.disabled ) {
      hideTop.sheet.disabled = false;
      document.getElementById('showTopToggle').innerHTML = '&uarr;&darr;';
    } else {
      hideTop.sheet.disabled = true;
      document.getElementById('showTopToggle').innerHTML = '&darr;&uarr;';
    }
  }
}

function show( trainTable ) {
  setcookie( 'tableName', trainTable._name, 30 );

  document.getElementById('timetable').deleteAllChildren();

  var table = document.createElement('table');

  table.cellSpacing = '0';
  table.cellPadding = '2px';
  table.style.textAlign = 'center';
  table.style.border = '1px solid black';
  table.id = 'table';

  for ( var i = 0 ; i < trainTable.trainArr.length ; ++i ) {
    var tr = document.createElement('tr');

    if ( i % 2 ) tr.style.backgroundColor = '#ddffff';

    var bold = 0;

    for ( var j = 0 ; j < trainTable.trainArr[i].length ; ++j ) {
      var td = document.createElement('td');

      if ( i == 0 ) {
        td.style.fontWeight = 'bold';
        td.style.borderBottom = '1px solid black';
      } else if ( j == 0 ) {
        td.style.fontWeight = 'bold';
        td.style.backgroundColor = 'lightgrey';

        td.style.cursor = 'pointer';
        td.clicked = false;
        td.onclick = function(td,rowIndex) { return function() {
            if ( td.clicked ) {
              td.style.backgroundColor='lightgrey';
              td.parentNode.style.backgroundColor=(i%2?'#ddffff':'');
              td.clicked = false;

              if ( trainTable.clickedRows[0] == rowIndex ) trainTable.clickedRows = [ trainTable.clickedRows[1] ];
              else trainTable.clickedRows = [ trainTable.clickedRows[0] ];
            } else {
              if ( trainTable.clickedRows.length == 2 ) return;

              td.style.backgroundColor='orange';
              td.parentNode.style.backgroundColor='orange';
              td.clicked = true;

              trainTable.clickedRows.push( rowIndex );
            }

            setcookie( trainTable._name + '.clickedRows', trainTable.clickedRows.join(','), 30 );

            for ( var i = 1 ; i < table.childNodes.length ; ++i ) {
              if ( ( trainTable.clickedRows.length == 2 && i < trainTable.clickedRows[0] && i < trainTable.clickedRows[1] ) ||
                   ( trainTable.clickedRows.length == 2 && i > trainTable.clickedRows[0] && i > trainTable.clickedRows[1] ) ) {
                //table.childNodes[i].style.display = 'none';
                table.childNodes[i].className = 'top';
              } else {
                //table.childNodes[i].style.display = 'table-row';
                table.childNodes[i].className = 'middle';
              }
            }

            updateNextTrain();
          }; }(td,i);
      }

      if ( parseInt(trainTable.trainArr[i][j].substr(0,2),10) > 11 ) td.style.fontWeight = 'bold';

      td.innerHTML = trainTable.trainArr[i][j].split(' ').join('&nbsp;');

      tr.appendChild( td );
    }

    table.appendChild( tr );
  }

  var a = getcookie( trainTable._name + '.clickedRows' );
  setcookie( trainTable._name + '.clickedRows', '', 30 );
  if ( a != null && a != '' ) {
    trainTable.clickedRows = [];
    var clickedRows = a.split(',');

    for ( var i = 0 ; i < clickedRows.length ; ++i ) {
      table.childNodes[ clickedRows[i] ].childNodes[0].onclick();
    }
  } else {
    trainTable.clickedRows = [];
  }

  document.getElementById('timetableHeader').setName( trainTable.name );
  document.getElementById('timetable').appendChild( table );

  updateNextTrain();
}

function updateNextTrain() {
  var table = document.getElementById('table');
  if ( table == null ) return;

  var now = new Date();

  //now.setHours( 5 + now.getMinutes() % 5 );
  //now.setMinutes( now.getSeconds() );

  for ( var j = 1 ; j < table.childNodes.length ; ++j ) {
    for ( var i = 1 ; i < table.childNodes[j].childNodes.length ; ++i ) {
      if ( table.childNodes[j].childNodes[0].clicked ) {
        var a = table.childNodes[j].childNodes[i].innerHTML;
        if ( a.split(':').length == 2 ) {
          var hours = parseInt(a.split(':')[0],10);
          var minutes = parseInt(a.split(':')[1],10);

          var origText = table.childNodes[j].childNodes[i].innerHTML.split('<br>')[0];

          if ( hours > now.getHours() ||
               hours == now.getHours() && minutes > now.getMinutes() ) {
            table.childNodes[j].childNodes[i].style.outline = '2px solid orange';
            table.childNodes[j].childNodes[i].style.backgroundColor = 'white';
            table.childNodes[j].childNodes[i].innerHTML = origText + '<br>+' + ((hours-now.getHours())*60 + (minutes-now.getMinutes())) + 'm';
            break;
          } else {
            table.childNodes[j].childNodes[i].style.outline = '';
            table.childNodes[j].childNodes[i].style.backgroundColor = '';
            table.childNodes[j].childNodes[i].innerHTML = origText;
          }
        }
      } else {
        table.childNodes[j].childNodes[i].style.outline = '';
        table.childNodes[j].childNodes[i].style.backgroundColor = '';
        table.childNodes[j].childNodes[i].innerHTML = table.childNodes[j].childNodes[i].innerHTML.split('<br>')[0];
      }
    }
  }

  /* Show or hide old trains */
  for ( var j = 0 ; j < table.childNodes.length ; ++j ) {
    var numShown = 0;
    for ( var i = 1 ; i < table.childNodes[j].childNodes.length ; ++i ) {
      var a = table.childNodes[ table.childNodes.length - 1 ].childNodes[i].innerHTML;
      if ( a.split(':').length == 2 ) {
        var hours = parseInt(a.split(':')[0],10);
        var minutes = parseInt(a.split(':')[1],10);

        if ( (numShown < 10 && hours > now.getHours()) ||
             (numShown < 10 && hours == now.getHours() && minutes > now.getMinutes()) ) {
	  table.childNodes[j].childNodes[i].className = 'center';
          ++numShown;
        } else {
	  table.childNodes[j].childNodes[i].className = 'left';
        }
      } else {
        table.childNodes[j].childNodes[i].className = 'left';
      }
    }
  }
}

function main() {
  var sel = document.getElementById('tableSelect');
  for ( var i = 0 ; i < trainTables.length ; ++i ) {
    var a = document.createElement('option');
    a.text = trainTables[i].name;
    a.value = trainTables[i]._name;
    sel.appendChild( a );
  }
  sel.onchange = function() { eval("show(" + sel.options[ sel.selectedIndex ].value + ")"); };

  document.getElementById('timetable').deleteAllChildren = function() {
    var div = document.getElementById('timetable');
    while ( div.firstChild ) { div.removeChild( div.firstChild ); }
  };

  document.getElementById('timetableHeader').setName = function(name) {
    var div = document.getElementById('timetableHeader');
    div.innerHTML = name;
  };

  var tableName = getcookie('tableName');
  if ( tableName ) { eval("show(" + tableName + ")"); sel.value = tableName; }
  else show( WeekdayNorth );

  window.setInterval( updateNextTrain, 60000 );

  loadTwitter(); window.setInterval( loadTwitter, 60000 );
}

/* Load the caltrain twitter feed */
function loadTwitter() {
  function ajax(url, callback) {
    var req;

    try { req = new XMLHttpRequest(); } catch ( e ) {
      try { req = new ActiveXObject("Msxml2.XMLHTTP"); } catch( e ) {
        try { req = new ActiveXObject("Microsoft.XMLHTTP"); } catch( e ) { return; }
      }
    }

    req.onreadystatechange = function() {
      if ( req.readyState == 4 ) {
        callback( req.status, req.responseText );
      }
    }

    req.open("GET",url,true);
    req.send(null);
  }

  ajax( 'twitter.php', function(a,b) {
    if ( a != 200 ) {
      document.getElementById('twitterFeed').appendChild( document.createTextNode("Error: failed to load twitter feed") );
      return;
    }

    var twitterFeed;
    eval( 'twitterFeed = ' + b );

    var table = document.createElement( 'table' );
    table.style.fontSize = '1.0em';

    var now = new Date();

    for ( var i = 0 ; i < twitterFeed.length ; ++i ) {
      var when = new Date(twitterFeed[i].created_at);
      var whenStr = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'][when.getDay()] + '&nbsp;'
                  + ('0'+when.getHours()).substr(-2,2) + ':' + ('0'+when.getMinutes()).substr(-2,2);
      var text = twitterFeed[i].text;

      if ( when.toDateString() != now.toDateString() ) continue;

      var tr = document.createElement( 'tr' );

      /* Process text for more consistency */
      // s/[sS][bB] ?([0-9][0-9][0-9])/SB\1/ s/[nN][bB] ?([0-9][0-9][0-9])/NB\1/
      text = text.replace( /sb ?(?=[0-9][0-9][0-9])/gi,  'SB' );
      text = text.replace( /nb ?(?=[0-9][0-9][0-9])/gi,  'NB' );
      text = text.replace( /\b(?=[0-9][0-9][02468]\b)/g, 'SB' );
      text = text.replace( /\b(?=[0-9][0-9][13579]\b)/g, 'NB' );
      text = text.replace( / T[0-9]?[0-9]:[0-9][0-9]$/,   ''   );
      text = text.replace( /([0-9]) ?mi?ns? /, '$1 min ' );

      var td = document.createElement('td');
      td.innerHTML = whenStr;
      td.style.textAlign = 'right';
      td.style.paddingRight = '10px';
      tr.appendChild( td );

      td = document.createElement('td');
      td.appendChild( document.createTextNode( text ) );
      tr.appendChild( td );

      table.appendChild( tr );

      //a += whenStr + ' &nbsp; ' + text + '<br>';
    }

    while ( document.getElementById('twitterFeed').firstChild ) document.getElementById('twitterFeed').removeChild( document.getElementById('twitterFeed').firstChild );
    document.getElementById('twitterFeed').appendChild( table );
  } );
}
</script>
</head>
<body onload="main()">
<div id="timetableHeader" style="text-align: center; font-weight: bold; font-size: 3.5em; background-color: lightgrey; width: 820px;"></div>
<div id="header_bar" style="background-color: lightgreen; text-align: left; padding: 10px; width: 800px;">
  <select id="tableSelect"></select>
  &nbsp; &nbsp;
  <span class="headerButton" onclick="showAllToggle('left-right')" id="showLeftToggle">&larr;&rarr;</span>
  &nbsp; &nbsp;
  <span class="headerButton" style="padding-left: 20px; padding-right: 20px;" onclick="showAllToggle('top-bottom')" id="showTopToggle">&uarr;&darr;</span>
</div>
<div id="timetable"></div>
<div id="twitterFeed" style="font-size: 1.2em; margin-top: 10px;"></div>
</body>
</html>
