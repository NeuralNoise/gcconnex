<?php
//api key
$params = array(
        'name' => 'params[apikey]',
        'id' => 'apikey',
        'class' => 'mrgn-bttm-sm',
        'value' => $vars['entity']->apikey,
    );

echo '<div class="basic-profile-field">';
echo '<label for="apikey">API key</label>';
echo elgg_view("input/text", $params);
echo '</div>';

//domain
$params = array(
        'name' => 'params[domain]',
        'id' => 'domain',
        'class' => 'mrgn-bttm-sm',
        'value' => $vars['entity']->domain,
    );

echo '<div class="basic-profile-field">';
echo '<label for="domain">Domain</label>';
echo elgg_view("input/text", $params);
echo '</div>';


//portal
echo '<div class="basic-profile-field">';
echo '<label for="portal_id">Portal</label>';
echo elgg_view("input/select", array(
        'name' => 'params[portal_id]',
        'id' => 'portal_id',
        'class' => 'mrgn-bttm-sm form-control',
        'options_values' => array(
            2100008988 => "GCconnex",
            2100008989 => "GCcollab",
        ),
        'value' => $vars['entity']->portal_id));
echo '</div>';

//product
echo '<div class="basic-profile-field">';
echo '<label for="product_id">Product</label>';
echo elgg_view("input/select", array(
        'name' => 'params[product_id]',
        'id' => 'product_id',
        'class' => 'mrgn-bttm-sm form-control',
        'options_values' => array(
            2100000289 => "GCconnex",
            2100000290 => "GCcollab",
        ),
        'value' => $vars['entity']->product_id));
echo '</div>';

if(isset($vars['entity']->portal_id)){
  echo '<div><p>To add the Freshdesk articles to the system, first press the fetch articles button, then press the save articles button after the articles have loaded.</p>';
  echo '<a class="article-button" id="fetch" href="#">Fetch Articles</a> <a class="article-button" id="save" href="#">Save Articles</a>';
  echo'<div class="article-message"></div></div>';
}

?>
<style>
  select {
    font: 120% Arial, Helvetica, sans-serif;
    padding: 5px;
    border: 1px solid #ccc;
    color: #666;
    border-radius: 5px;
    margin: 0;
    width: 98%;
  }

  .article-button {
    padding: 8px 16px;
    border: 2px solid black;
    border-radius: 3px;
  }

  .article-button:hover {
    background: #eee;
  }

  .article-message {
    margin: 15px;
  }
</style>

<div id="results-en"><div id="categories-en"></div></div>
<div id="results-fr"><div id="categories-fr"></div></div>

<script>


$('#fetch').on('click', function(){

  var yourdomain = <?php echo '"'.$vars['entity']->domain.'"'; ?>;
  var api_key = <?php echo '"'.$vars['entity']->apikey.'"'; ?>;
  var portal_id = <?php echo $vars['entity']->portal_id; ?>;
  var acceptedCategoriesEN = [];
  var acceptedCategoriesFR = [];

  $.ajax(
    {
      url: "https://"+yourdomain+".freshdesk.com/api/v2/solutions/categories/en",
      type: 'GET',
      contentType: "application/json; charset=utf-8",
      dataType: "json",
      headers: {
        "Authorization": "Basic " + btoa(api_key + ":x")
      },
      success: function(data, textStatus, jqXHR) {
        var category = JSON.parse(JSON.stringify(data));
        for (var c = 0; c < category.length; c++){
          if($.inArray(portal_id, category[c].visible_in_portals) > -1){ //categories that appear on GCconnex
            acceptedCategoriesEN.push(category[c]);
          }
        }

        acceptedCategoriesEN = uniqueObjects(acceptedCategoriesEN); //removed duplicate categories

        $.each(acceptedCategoriesEN, function (key,value) {

             $('#categories-en').append("<div class='category-card'><div class='article-cat'><div class='heading'><h3 >"  +value.name + "</h3></div><div><ul class='folders' id='" + value.id + "-en'></ul> </div></div></div></div>");

             $.ajax(
               {
                 url: "https://"+yourdomain+".freshdesk.com/api/v2/solutions/categories/"+value.id+"/folders/en",
                 type: 'GET',
                 contentType: "application/json; charset=utf-8",
                 dataType: "json",
                 headers: {
                   "Authorization": "Basic " + btoa(api_key + ":x")
                 },
                 success: function(data, textStatus, jqXHR) {
                   var folder = JSON.parse(JSON.stringify(data));

                    $.each(folder, function (key2,value2) {

                      if(value2.visibility == 1){
                        $('#'+value.id+'-en').append("<li class='folder'><a onclick='displayFolder(this)' href='#"+ value2.id + "-en'><div>"  +value2.name + "</div></a></li>");
                        $('#results-en').append("<div class='folder-display' id='" + value2.id + "-en'><div class='heading-panel'><a class='icon-unsel' onclick='displayCategories(this)' href='#categories-en'><i class='fa fa-arrow-left fa-lg' aria-hidden='true'></i><span class='wb-inv'>"+elgg.echo('freshdesk:knowledge:explore:return')+"</a> <div><h3>"+value2.name+"</h3></div></div></div>");
                      }

                      $.ajax({

                          url: "https://"+yourdomain+".freshdesk.com/api/v2/solutions/folders/"+value2.id+"/articles/en",
                          type: 'GET',
                          contentType: "application/json; charset=utf-8",
                          dataType: "json",
                          headers: {
                            "Authorization": "Basic " + btoa(api_key + ":x")
                          },
                          success: function(data, textStatus, jqXHR) {
                            var article = JSON.parse(JSON.stringify(data));

                              $('#'+value2.id+'-en').append("<div class='article-panel'></div>");
                              var list = "<ul>";
                              $.each(article, function (key3,value3) {//<span class='collapse-plus fa fa-plus-square-o fa-lg' aria-hidden='true'></span>
                                if(value3.status == 2){
                                  list += "<li class='article-listing'><a class='head-toggle collapsed' href='#"+ value3.id + "-en' data-toggle='collapse' aria-expanded='false' aria-controls='"+ value3.id +"-en'><div><h5><span class='collapse-plus fa fa-minus-square-o fa-lg' aria-hidden='true'></span>" + value3.title + "</h5></div></a><div id='" + value3.id +
                                  "-en' class='collapse article-content'>" + value3.description + " </div> </div></li>";
                                }
                              });
                              list += '</ul><a onclick="displayCategories(this)" href="#categories-en" class="wb-inv">'+elgg.echo('freshdesk:knowledge:explore:return')+'</a>';
                              $('#'+value2.id+"-en .article-panel").append(list);
                          }
                        });

                    });

                 }
               }

             );

         });
      }
    });

    $.ajax(
      {
        url: "https://"+yourdomain+".freshdesk.com/api/v2/solutions/categories/fr",
        type: 'GET',
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        headers: {
          "Authorization": "Basic " + btoa(api_key + ":x")
        },
        success: function(data, textStatus, jqXHR) {
          var category = JSON.parse(JSON.stringify(data));
          for (var c = 0; c < category.length; c++){
            if($.inArray(portal_id, category[c].visible_in_portals) > -1){ //categories that appear on GCconnex
              acceptedCategoriesFR.push(category[c]);
            }
          }

          acceptedCategoriesFR = uniqueObjects(acceptedCategoriesFR); //removed duplicate categories

          $.each(acceptedCategoriesFR, function (key,value) {

               $('#categories-fr').append("<div class='category-card'><div class='article-cat'><div class='heading'><h3 >"  +value.name + "</h3></div><div><ul class='folders' id='" + value.id + "-fr'></ul> </div></div></div></div>");

               $.ajax(
                 {
                   url: "https://"+yourdomain+".freshdesk.com/api/v2/solutions/categories/"+value.id+"/folders/fr",
                   type: 'GET',
                   contentType: "application/json; charset=utf-8",
                   dataType: "json",
                   headers: {
                     "Authorization": "Basic " + btoa(api_key + ":x")
                   },
                   success: function(data, textStatus, jqXHR) {
                     var folder = JSON.parse(JSON.stringify(data));

                      $.each(folder, function (key2,value2) {

                        if(value2.visibility == 1){
                          $('#'+value.id+'-fr').append("<li class='folder'><a onclick='displayFolder(this)' href='#"+ value2.id + "-fr'><div>"  +value2.name + "</div></a></li>");
                          $('#results-fr').append("<div class='folder-display' id='" + value2.id + "-fr'><div class='heading-panel'><a class='icon-unsel' onclick='displayCategories(this)' href='#categories-fr'><i class='fa fa-arrow-left fa-lg' aria-hidden='true'></i><span class='wb-inv'>"+elgg.echo('freshdesk:knowledge:explore:return')+"</a> <div><h3>"+value2.name+"</h3></div></div></div>");
                        }

                        $.ajax({

                            url: "https://"+yourdomain+".freshdesk.com/api/v2/solutions/folders/"+value2.id+"/articles/fr",
                            type: 'GET',
                            contentType: "application/json; charset=utf-8",
                            dataType: "json",
                            headers: {
                              "Authorization": "Basic " + btoa(api_key + ":x")
                            },
                            success: function(data, textStatus, jqXHR) {
                              var article = JSON.parse(JSON.stringify(data));

                                $('#'+value2.id+'-fr').append("<div class='article-panel'></div>");
                                var list = "<ul>";
                                $.each(article, function (key3,value3) {//<span class='collapse-plus fa fa-plus-square-o fa-lg' aria-hidden='true'></span>
                                  if(value3.status == 2){
                                    list += "<li class='article-listing'><a class='head-toggle collapsed' href='#"+ value3.id + "-fr' data-toggle='collapse' aria-expanded='false' aria-controls='"+ value3.id +"-fr'><div><h5><span class='collapse-plus fa fa-minus-square-o fa-lg' aria-hidden='true'></span>" + value3.title + "</h5></div></a><div id='" + value3.id +
                                    "-fr' class='collapse article-content'>" + value3.description + " </div> </div></li>";
                                  }
                                });
                                list += '</ul><a onclick="displayCategories(this)" href="#categories-fr" class="wb-inv">'+elgg.echo('freshdesk:knowledge:explore:return')+'</a>';
                                $('#'+value2.id+"-fr .article-panel").append(list);
                            }
                          });

                      });

                   }
                 }

               );

           });
        }
      });

});

$('#save').on('click', function(){
  var helpdeskEN = $("#results-en").html();
  var helpdeskFR = $("#results-fr").html();

  $('#results-en').hide();
  $('#results-fr').hide();

  var translations = { "en" : helpdeskEN, "fr" : helpdeskFR };

  var obj = {"html": JSON.stringify(translations) };

  $.ajax({
          type: "POST",
          dataType : 'json',
          async: false,
          url: elgg.normalize_url('/mod/freshdesk_help/actions/articles/save.php'),
          data: obj,
          success: function () { $('.article-message').append('Articles Saved'); $('#results-en').html(''); $('#results-fr').html(''); },
          failure: function() { $('.article-message').append('Something went wrong'); }
      });
});
</script>
