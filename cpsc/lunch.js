// Makes lunch selection easier at tastenutrition.com by auto-selecting
// (or skipping) milk selection and clicking "Submit" for you.

var breadfu_original_menuprompt;
var breadfu_inited = false;

var breadfu_favorites = {};

(function() {
  function make_checkbox(label, checked) {
    var input = document.createElement('input');
    input.type = 'checkbox';
    input.checked = checked;
    var div = document.createElement('div');
    div.style.display = 'inline-block';
    div.style.borderLeft = '1px solid black';
    div.style.marginLeft = '20px';
    div.style.paddingLeft = '20px';
    div.appendChild(document.createTextNode(label));
    div.appendChild(input);
    return {div: div, input: input};
  }

  var lastdate = null;

  // Dialog bar at top of page
  var dialog = document.createElement('div');
  dialog.style.position = 'fixed';
  dialog.style.width = '100%';
  dialog.style.height = '30px';
  dialog.style.zIndex = '100';
  dialog.style.backgroundColor = '#eed';
  dialog.style.outline = '1px solid black';
  dialog.style.top = '0';
  dialog.style.left = '0';
  dialog.style.lineHeight = '30px';
  dialog.style.paddingLeft = '10px';
  dialog.innerHTML = '<b>EZTasteNutrition</b>';

  // Milk selector
  var milk = make_checkbox('Milk? ', true);
  dialog.appendChild(milk.div);

  // Auto-advance selector
  var autoadvance = make_checkbox('Auto-Advance? ', true);
  dialog.appendChild(autoadvance.div);

  document.body.appendChild(dialog);
  document.body.style.marginTop = '30px';

  function maybe_autoadvance() {
    if (!autoadvance.input.checked) return;

    // Find the div for 'lastdate'
    var days = document.querySelectorAll('td.menucell > div[onclick]');
    for (var i = 0; i < days.length; ++i) {
      if (days[i].getAttribute('onclick') == "menuprompt('" + lastdate + "'); return false;") {
        break;
      }
    }
    if (i + 1 < days.length) {
      days[i+1].click();
    }
  }

  function breadfu_init_easy_lunch() {
    if (breadfu_inited) return;
    breadfu_inited = true;
    document.querySelector('#menubox iframe').onload = function() {
      var popup = document.querySelector('#menubox iframe').contentDocument;
      var submit = popup.querySelector('html a:not(.epg-added)');
      var inputs = popup.querySelectorAll('html input');
      var inputMap = [...inputs].map((f, i) =>
        f.parentNode.nextElementSibling ?
        {input: f, label: f.parentNode.nextElementSibling.textContent.trim(), index: i} :
        {input: f, label: f.parentNode.textContent.trim()}
      );

      var nonMilk = inputMap.filter(f => f.label && !f.label.match(/^Organic Milk/));

      nonMilk.filter(f => {
        if (f.input.parentNode.nextElementSibling) {
          var a = popup.createElement('a');
          a.textContent = f.label;
          a.className = 'epg-added';
          a.style.textDecoration = 'underline';
          if (breadfu_favorites[a.textContent]) a.style.color = '#a0f'
          else a.style.color = 'blue';
          a.style.cursor = 'pointer';
          a.onclick = () => {
            breadfu_favorites[a.textContent] = true;
            f.input.click();
            if (milk.input.checked && inputMap[f.index + 1].input) {
              inputMap[f.index + 1].input.click();
            }
            submit.click();
            window.setTimeout(maybe_autoadvance, 0);
          }

          f.input.parentNode.nextElementSibling.replaceChild(
            a,
            f.input.parentNode.nextElementSibling.firstChild
          );
        }
      });
    }
  }

  if (!document.URL.startsWith('https://www.tastenutrition.com/school_menu.asp')) {
    alert('This only works on the TasteNutrition site, sorry!');
    return;
  }

  if (document.querySelector('#menubox iframe')) {
    breadfu_init_easy_lunch();
  }

  breadfu_original_menuprompt = breadfu_original_menuprompt || menuprompt;
  menuprompt = function(thedate) {
    lastdate = thedate;
    breadfu_original_menuprompt(thedate);
    breadfu_init_easy_lunch();
  }
})();
void(0)
