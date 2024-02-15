console.log(htmlcacher);
const clearCache = async () => {
  var form_data = new FormData();
  form_data.append('action', 'htmlcacher_clear');
  const response = await fetch(
    htmlcacher.ajaxurl,
    {
      method: 'POST',
      body: form_data,
    }
  )
  const data = await response.json();
  if(data.message){
    alert(data.message);
  }
}
document.addEventListener('click', (event) => {
  if (event.target.closest('[href*="?page=htmlcache_clear_total"]')) {
    event.preventDefault();
    clearCache();
  }
});
// jQuery(function($) {
//   $(document).on('click','[href*="?page=htmlcache_clear_total"]',async function (event) {
//     // $.ajax({
//     //   url: ajaxurl,
//     //   type: 'POST',
//     //   data: {
//     //     'action': 'htmlcacher_clear'
//     //   },
//     //   dataType: 'json',
//     // }).done(function(data) {
//     //   console.log(data);
//     //   console.log("success");
//     // })
//     //   .fail(function() {
//     //     console.log("error");
//     //   })
//     //   .always(function() {
//     //     console.log("complete");
//     //   });
//   });
// });
