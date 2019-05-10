// Makes lunch selection easier at tastenutrition.com by auto-selecting
// (or skipping) milk selection and clicking "Submit" for you.

var breadfu_original_menuprompt;

(function() {
  var milk = confirm('Auto-select milk? (Ok => yes, Cancel => no)');

  // Give a hint that this is active.
  document.body.style.backgroundColor = '#ffe';

  function breadfu_init_easy_lunch() {
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
          a.style.color = 'blue';
          a.style.cursor = 'pointer';
          a.onclick = () => {
            f.input.click();
            if (milk && inputMap[f.index + 1].input) {
              inputMap[f.index + 1].input.click();
            }
            submit.click();
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
  } else {
    breadfu_original_menuprompt = breadfu_original_menuprompt || menuprompt;
    menuprompt = function(thedate) {
      menuprompt = breadfu_original_menuprompt;
      breadfu_original_menuprompt(thedate);
      breadfu_original_menuprompt = undefined;
      breadfu_init_easy_lunch();
    }
  }
})();
void(0)
