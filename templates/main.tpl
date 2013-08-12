<!DOCTYPE html>
<html>
  <head>
    <title>calibrePage</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="libs/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">

    <link href="libs/main.css" rel="stylesheet" media="screen">
  </head>
  <body>



    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".nav-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#">calibrePage</a>
        <div class="nav-collapse collapse">
          <ul class="nav navbar-nav">
              <li class="navbar-text">Collections:</li>
              {foreach $COLLECTIONS_DIRS as $COLLECTION}
               <li {if $smarty.get.collection == $COLLECTION}class="active"{/if}><a href="index.php?collection={$COLLECTION}">{$COLLECTION}</a></li>
             {foreachelse}
               <li>No items found</li>
             {/foreach}
          </ul>
        </div>
      </div>
    </div>

    <div class="container">
      <div class="row starter-template">
        <div class="col-lg-12">

          {if isset($smarty.get.collection) == false}
            <div class="alert alert-info">Please select a collection from the top</div>
          {else}
            <div>
              {if empty($DISPLAY_BOOKS) == false}

                {if $PAGES > 1}
                  <ul class="pagination">
                    {if $smarty.get.page > 0}
                      <li><a href="index.php?collection={$smarty.get.collection}&page={$smarty.get.page-1}">&laquo;</a></li>
                    {/if}
                    {for $idx = 0 to $PAGES-1}
                      <li {if $idx == $smarty.get.page}class="active"{/if}><a href="index.php?collection={$smarty.get.collection}&page={$idx}">{$idx+1}</a></li>
                    {/for}
                    {if $smarty.get.page < $PAGES-1}
                      <li><a href="index.php?collection={$smarty.get.collection}&page={$smarty.get.page+1}">&raquo;</a></li>
                    {/if}

                  </ul>
                {/if}

                <table class="table table-condensed">
                  <thead>
                    <tr>
                      <th></th>
                      <th>Title</th>
                      <th>Author</th>
                      <th>Files</th>
                    </tr>
                  </thead>
                  <tbody>
                    {foreach $DISPLAY_BOOKS as $DISPLAY_BOOK}
                      <tr>
                        <td>
                          {if $DISPLAY_BOOK->book['has_cover'] == 1}
                            <img src="collections/{$smarty.get.collection}/{$DISPLAY_BOOK->book['path']}/cover.jpg" class="book_cover" />
                          {/if}
                        </td>
                        <td>{$DISPLAY_BOOK->book['title']}</td>
                        <td>{$DISPLAY_BOOK->book['author_sort']}</td>
                        <td>
                          {foreach $DISPLAY_BOOK->data as $BOOK_DATA}
                            <a href="collections/{$smarty.get.collection}/{$DISPLAY_BOOK->book['path']}/{$BOOK_DATA['name']}.{$BOOK_DATA['format']|lower}">{$BOOK_DATA['format']}</a>
                          {/foreach}
                        </td>
                      </tr>
                    {/foreach}
                  </tbody>
                </table>
              {/if}
            </div>
          {/if}
        </div>
      </div>

    </div> <!-- /container -->

    <!-- JavaScript plugins (requires jQuery) -->
    <script src="libs/jquery-1.10.2.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="libs/bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>