<html>
<head>
<? if ( !isset( $_REQUEST ) || !isset( $_REQUEST['dict'] ) ) { ?>
</head><body>
<br><br><br><br><br><center style="font-size: 2.0em;">
<a href="?dict=list8_4">Four to Eight Letter Words</a><br><br><br>
<a href="?dict=list8">Three to Eight Letter Words</a></center>
</body></html>
<? exit(); ?>
<? } else if ( $_REQUEST['dict'] == 'list8_4' ) { ?>
<script src="list8_4.js"></script>
<? } else if ( $_REQUEST['dict'] == 'list8' ) { ?>
<script src="list8.js"></script>
<? } else { exit(); } ?>
<script type="text/javascript">
var wordList = {};

function doSolver() {
  var targetWord = {};

  for ( var i = 0 ; i < myWordArr_.length ; ++i ) {
    var letter = myWordArr_[i].letter;
    if ( targetWord[letter] ) targetWord[letter]++;
    else targetWord[letter] = 1;
  }

  for ( var i = 0 ; i < wordArr.length ; ++i ) {
    var word = wordArr[i].split('');
    var wordMatch = {};

    for ( var j = 0 ; j < word.length ; ++j ) {
      if ( wordMatch[ word[j] ] ) wordMatch[ word[j] ]++;
      else wordMatch[ word[j] ] = 1;

      if ( !targetWord[ word[j] ] ) break;
      if ( wordMatch[ word[j] ] > targetWord[ word[j] ] ) break;
    }

    if ( j == word.length ) {
      solvedWords_.push( word.join('') );
      maxScore_ += word.length;
      document.getElementById('Score').innerHTML = myScore_ + '/' + maxScore_;
    }
  }

  updateClueHistory();
}

var myWordArr_;
var clueEntry_;
var clueHistory_;
var myClueHistory_;
var myScore_;
var maxScore_;
var solvedWords_;
var initialHtml_;

function nextPuzzle() {
  if ( document.getElementById('RewardArea').innerHTML == '' &&
       clueHistory_.length != 0 ) {
    if ( !confirm("Would you like a new puzzle?") ) {
      return;
    }
  }

  document.body.innerHTML = initialHtml_;
  main();
}

function main() {
  /* Reset the globals */
  myWordArr_ = new Array();
  clueEntry_ = new Array();
  clueHistory_ = new Array();
  myClueHistory_ = {};
  myScore_ = 0;
  maxScore_ = 0;
  solvedWords_ = new Array();
  initialHtml_ = document.body.innerHTML;

  var rnd = Math.floor(Math.random() * wordArr.length);

  var myWord = wordArr[rnd];
  var myWordArr = myWord.split('');
  myWordArr.sort();

  for ( var i = 0 ; i < wordArr.length ; ++i ) { wordList[wordArr[i]] = 1; }

  for ( var i = 0 ; i < myWordArr.length ; ++i ) {
    var div = document.createElement('div');
    div.appendChild( document.createTextNode( myWordArr[i].toUpperCase() ) );
    div.letter = myWordArr[i];
    div.className = 'Letter';
    document.getElementById('Anagram').appendChild( div );
    myWordArr_[i] = div;
  }

  newKey(' ');
  window.setTimeout( doSolver, 1 );
  document.getElementById('ClueEntryInput').focus();
}

var toChar = [ 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z' ];

function keypress(event) {
  //console.log(event);
  //console.log( event.ctrlKey );
  if ( event.ctrlKey && event.keyCode == 8 ) { newKey( 27 ); return false; }

  if ( event.ctrlKey || event.altKey ) return true;

  if ( event.charCode >= 97 && event.charCode <= 122 ) newKey( toChar[event.charCode-97] );
  else if ( event.charCode >= 65 && event.charCode <= 90 )  newKey( toChar[event.charCode-65] );
  else if ( event.keyCode >= 97 && event.keyCode <= 122 ) newKey( toChar[event.keyCode-97] );
  else if ( event.keyCode >= 65 && event.keyCode <= 90 )  newKey( toChar[event.keyCode-65] );
  else if ( event.charCode == 32 ) newKey( ' ' );
  else if ( event.keyCode == 32 ) newKey( ' ' );
  else if ( event.keyCode == 13 || event.keyCode == 10 ) newKey( '\n' );
  else if ( event.keyCode == 8 || event.keyCode == 127 ) newKey( 8 );
  else if ( event.keyCode == 27 ) newKey( 27 );
  //else document.getElementById('Errors').innerHTML += 'Unknown key: ' + event.charCode + ' / ' + event.keyCode + '<br>';

  updateClueEntry();

  return false;
}

function tryWord( aWord ) {
  if ( wordList[aWord] ) {
    if ( myClueHistory_[aWord] ) return false;

    myClueHistory_[aWord] = 1;
    myScore_ += aWord.length;
    document.getElementById('Score').innerHTML = myScore_ + '/' + maxScore_;

    if ( myScore_ == maxScore_ ) {
      document.getElementById('PlayArea').style.display = 'none';
      document.getElementById('RewardArea').innerHTML = 'Congrats. You Are Winnar!<br><img src="reward.php?' + Math.random() + '" style="height : 100%;"></img>';
      document.getElementById('RewardArea').style.display = 'block';
    }
    return true;
  } else {
    return false;
  }
}

function updateClueHistory() {
  document.getElementById('ClueHistory').style.height = '';

  var clues = {};

  for ( var i = 0 ; i < solvedWords_.length ; ++i ) {
    if ( clues[ solvedWords_[i].length ] ) {
      clues[ solvedWords_[i].length ].push( { word: solvedWords_[i], picked: (myClueHistory_[solvedWords_[i]]?myClueHistory_[solvedWords_[i]]:0) } );
    } else {
      clues[ solvedWords_[i].length ] = [ { word: solvedWords_[i], picked: (myClueHistory_[solvedWords_[i]]?myClueHistory_[solvedWords_[i]]:0) } ];
    }
  }

  document.getElementById('ClueHistory').innerHTML = '';
  var column = document.createElement('div'); column.className = 'ClueHistoryColumn';
  document.getElementById('ClueHistory').appendChild( column );

  //var height = 0;

  for ( var i = 3 ; i <= myWordArr_.length ; ++i ) {
    for ( var j = 0 ; clues[i] && j < clues[i].length ; ++j ) {
      var parentDiv = document.createElement('div'); parentDiv.className='Word';
      var word;
      var className;
      if ( clues[i][j].picked == 1 ) { // user picked
        word = clues[i][j].word;
        className = 'Letter Solved';
        if ( word.length == myWordArr_.length ) className = 'Letter SolvedLong';
      } else if ( clues[i][j].picked == 2 ) { // user hit "solve" button
        word = clues[i][j].word;
        className = 'Letter Cheated';
      } else {
        word = '?????????????????????'.substr(1,i);
        className = 'Letter Unsolved';
      }

      for ( var k = 0 ; k < word.length ; ++k ) {
        var div = document.createElement('div'); div.className = className; div.innerHTML = word.charAt(k).toUpperCase();
        parentDiv.appendChild( div ); //document.createTextNode( word[k] ) );
      }
      //var div = document.createElement('div'); div.style.clear = 'both';

      if ( column.childNodes.length >= 15 ) {
        //height = Math.max(height,column.offsetHeight);
        column = document.createElement('div'); column.className = 'ClueHistoryColumn';
        document.getElementById('ClueHistory').appendChild( column );
      }

      column.appendChild( parentDiv );
    }
  }

  //height = Math.max(height,column.offsetHeight);

  //var height = document.getElementById('ClueHistory').offsetHeight;
  //var screenH = document.body.clientHeight - document.getElementById('ClueHistory').offsetTop - 10;

  //var zoomFactor = Math.min( Math.max( screenH/height, 0.5 ), 1 ).toFixed(2);

  //console.log( 'height=' + height + ' screenH=' + screenH + ' zoom=' + zoomFactor );
  //document.getElementById('ClueHistory').style.MozTransform='scale(' + zoomFactor +')';
  //document.getElementById('ClueHistory').parentNode.style.height = Math.ceil(height*zoomFactor) + 'px';
  //document.getElementById('ClueHistory').parentNode.style.overflow = 'hidden';
}

function updateClueEntry() {
  //document.getElementById('ClueEntry').innerHTML = clueEntry_.join('');
  document.getElementById('ClueEntryInput').value = clueEntry_.join('');

  for ( var i = 0 ; i < myWordArr_.length ; ++i ) { myWordArr_[i].style.visibility = 'visible'; }
  for ( var i = 0 ; i < clueEntry_.length ; ++i ) {
    for ( var j = 0 ; j < myWordArr_.length ; ++j ) {
      if ( myWordArr_[j].letter == clueEntry_[i] && myWordArr_[j].style.visibility != 'hidden' ) {
        myWordArr_[j].style.visibility = 'hidden';
        break;
      }
    }
    if ( j == myWordArr_.length ) return false;
  }
  return true;
}

function flashClueEntry(color) {
  document.getElementById('ClueEntry').style.backgroundColor = color;
  window.setTimeout( function() { document.getElementById('ClueEntry').style.backgroundColor = 'white'; }, 200 );
}

function newKey(a) {
    if ( a == '\n' ) {
      if ( clueEntry_.length == 0 ) return;

      if ( myClueHistory_[ clueEntry_.join('') ] ) {
        flashClueEntry('purple');
      } else if ( tryWord( clueEntry_.join('') ) ) {
        if ( clueEntry_.length == myWordArr_.length ) {
          clueHistory_.unshift( '<font color="purple">' + clueEntry_.join('') + '</font>' );
        } else {
          clueHistory_.unshift( clueEntry_.join('') );
        }
        clueEntry_ = new Array();
        updateClueHistory();
        updateClueEntry();
      } else {
        flashClueEntry('red');
      }
    } else if ( a == 8 ) {
      if ( clueEntry_.length ) clueEntry_.pop();
      updateClueEntry();
    } else if ( a == 27 ) {
      clueEntry_ = new Array();
      updateClueEntry();
    } else if ( a == ' ' ) {
      var a = [];
      var Anagram = document.getElementById('Anagram');
      while ( Anagram.childNodes.length ) {
        var j = Math.floor(Math.random() * Anagram.childNodes.length);
        a.push( Anagram.childNodes[j] );
        Anagram.removeChild( Anagram.childNodes[j] );
      }
      for ( var i = 0 ; i < a.length ; ++i ) { Anagram.appendChild( a[i] ); }
    } else {
      //var oldClueEntry = clueEntry_;
      clueEntry_.push( a );
      //clueEntry_ = document.getElementById('ClueEntryInput').value.split('');
      if ( !updateClueEntry() ) {
        //clueEntry_ = oldClueEntry;
        clueEntry_.pop();
        updateClueEntry();
        flashClueEntry('red');
      }
    }
}

function solvePuzzle() {
  if (!confirm("Would you like to see the answers?")) return;

  for ( var i = 0 ; i < solvedWords_.length ; ++i ) {
    if ( !myClueHistory_[ solvedWords_[i] ] ) {
      myClueHistory_[ solvedWords_[i] ] = 2;
      clueHistory_.unshift( '<font color="lightgrey">' + solvedWords_[i] + '</font>' );
    }
  }
  updateClueHistory();
  document.getElementById('SolvePuzzle').style.visibility = 'hidden';
}
</script>
<style type="text/css">
#Anagram {
  height : 64px;
}
#ClueHistory,
#ClueEntry,
#Anagram,
#Score {
  font-size : 3.0em;
  padding-left : 30px;
}
#ClueEntry {
  padding-top : 15px;
  padding-bottom : 15px;
  height : 64px;
}
#ClueEntryInput {
  height : 1.3em;
  font-size : 1.0em;
}
#ClueHistory {
  /*-moz-transform : scale(0.75);*/
  /*-moz-transform-origin : left top;*/
  /*zoom : 0.5;*/
}
.ClueHistoryColumn {
  /*border : 1px solid blue;*/
  /*width : 33%;*/
  float : left;
  margin-right : 15px;
  margin-left : 15px;
  /*-moz-transform-origin : left top;*/
  /*-moz-transform : scale(0.5);*/
}
#Score {
  position : absolute;
  left : 50%;
  top : 0;
  width : 20%;
  text-align : right;
  padding-left : 0;
  padding-top : 16px;
  margin-right : 30px;
}
#Anagram {
  float : left;
}
#NextPuzzle {
  height : 60px;
  padding-top : 4px;
  padding-left : 20px;
  float : left;
  font-size : 3.0em;
  color : orange;
  cursor : pointer;
}
#SolvePuzzle {
  height : 60px;
  padding-top : 4px;
  padding-left : 20px;
  float : left;
  font-size : 3.0em;
  color : purple;
  cursor : pointer;
}
.Letter {
  background-image : url(block3.png);
  width : 64px;
  height : 60px;
  //height : 64px; /* ie only */
  text-align : center;
  padding-top : 4px;
  padding-left : 0;
  float : left;
}
.Unsolved {
  background-position : 0 -96;
  color : grey;
  width : 32px;
  height : 30px;
  //height : 32px; /* ie only */
  padding-top : 2px;
  font-size : 0.5em;
  opacity : 0.5;
}
.Solved {
  background-position : 0 -64;
  color : black;
  width : 32px;
  height : 30px;
  //height : 32px; /* ie only */
  padding-top : 2px;
  font-size : 0.5em;
}
.SolvedLong {
  background-position : -32 -64;
  color : purple;
  width : 32px;
  height : 30px;
  //height : 32px; /* ie only */
  padding-top : 2px;
  font-size : 0.5em;
}
.Cheated {
  background-position : -32 -96;
  color : green;
  width : 32px;
  height : 30px;
  //height : 32px; /* ie only */
  padding-top : 2px;
  font-size : 0.5em;
  opacity : 0.8;
}
.Word {
  height : 34px;
  position : relative;
}
#Errors {
  font-size : 3.0em;
  color : red;
  display : none;
}
#RewardArea {
  font-size : 3.0em;
  color : blue;
  text-align : center;
  padding-top : 15px;
  width : 100%;
  height : 80%;
  padding-left : 0;
  display : none;
}
</style>
</head>
<body onload="main()">
<div style="height: 64px;">
  <div id="NextPuzzle" onclick="nextPuzzle()">Next</div>
  <div id="SolvePuzzle" onclick="solvePuzzle()">Solve</div>
</div>
<div style="height: 64px;">
  <div id="Anagram"></div>
</div>
<div id="PlayArea">
<div id="ClueEntry"><form action="javascript:newKey('\n')"><input type="text" id="ClueEntryInput" style="width: 80%;" onkeydown="return keypress(event)" onkeyup="/*keypress(event);*/return false"></form></input></div>
<div><div id="ClueHistory"></div></div>
<div id="Score"></div>
<div id="Errors"></div>
</div>
<div id="RewardArea"></div>
</body>
</html>
