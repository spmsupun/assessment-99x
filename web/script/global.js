$(function () {

  getBooks($('#book_form').serializeArray());
  showCartBar();

  //book search/sort/get
  $(document).on('change', '#book_form *', function (e) {
    getBooks($('#book_form').serializeArray());
  });

  //books views
  $(document).on('click', '#grid', function (e) {
    e.preventDefault();
    display('grid');
  });

  $(document).on('click', '#list', function (e) {
    e.preventDefault();
    display('list');
  });

  $(document).on('click', '.tree>li>a', function (e) {
    $('.tree>li>a').removeClass('selected');
    $(this).addClass('selected');
    getBooks({category: $(this).data('id')});
    $('.cat-name').html($(this).html());
    e.preventDefault();
  });

  //add to cart
  $(document).on('click', '.ajax_add_to_cart_button', function (e) {
    addToCart($(this).data('id'));
    e.preventDefault();
  });  //remove from  cart
  $(document).on('click', '.remove_book', function (e) {
    removeFromCart($(this).data('id'));
    e.preventDefault();
  });

  $(document).on('click', '.add-coupon', function (e) {
    $.confirm({
      title: 'Enter Your Coupon!',
      content: '<input type="text" class="coupon form-control">',
      theme: 'modern',
      buttons: {
        confirm: function () {

          var coupon = this.$content.find('.coupon').val();

          $.ajax({
            url: 'ajax/set/coupon',
            type: 'post',
            data: {coupon: coupon},
            complete: function (xhr, status) {
                $.alert({
                  title: xhr.responseJSON.msg,
                  type: 'green',
                  content: '',
                  theme: 'modern',
                  buttons: {
                    OK: function () {
                      location.reload();

                    }
                  }
                });
            }
          });
        },
        cancel: function () {}
      }
    });
    e.preventDefault();
  });

  $(document).on('click', '.remove-coupon', function (e) {
    $.confirm({
      title: 'Remove Coupon!',
      content: 'You sure you want to remove coupon?',
      theme: 'modern',
      buttons: {
        confirm: function () {
          $.ajax({
            url: 'ajax/remove/coupon',
            type: 'post',
            complete: function (xhr, status) {
                $.alert({
                  title: xhr.responseJSON.msg,
                  type: 'green',
                  theme: 'modern',
                  buttons: {
                    OK: function () {
                      location.reload();

                    }
                  }
                });
              location.reload();
            }
          });
        },
        cancel: function () {}
      }
    });
    e.preventDefault();
  });

});

function getBooks (filters) {
  $.ajax({
    url: 'ajax/get/books',
    type: 'get',
    data: filters,
    complete: function (xhr, status) {
      $('#books_container').html(xhr.responseText);
      $('.heading-counter').html('There are ' + $('.product_list li').length + ' books in the store!');
    }
  });
}

function addToCart (bookId) {

  $.ajax({
    url: 'ajax/add/cart',
    type: 'post',
    data: {bookId: bookId},
    complete: function (xhr, status) {
      if (status === 'success') {
        showCartBar();
        getBooks($('#book_form').serializeArray());
        $.alert({
          title: 'Success',
          type: 'green',
          content: xhr.responseJSON.msg,
          theme: 'modern'
        });
      }
    }
  });
}

function removeFromCart (bookId) {
  $.ajax({
    url: 'ajax/remove/cart',
    type: 'post',
    data: {bookId: bookId},
    complete: function (xhr, status) {
      if (status === 'success') {
        getBooks($('#book_form').serializeArray());
        showCartBar();
        $.alert({
          title: 'Success',
          type: 'green',
          content: xhr.responseJSON.msg,
          theme: 'modern'
        });
      }
    }
  });
}

function showCartBar () {
  $.ajax({
    url: 'ajax/get/cart',
    type: 'get',
    complete: function (xhr, status) {


      if (xhr.responseJSON.count) {
        $('.ajax_cart_quantity').html(xhr.responseJSON.count);
        $('.ajax_cart_total').html(xhr.responseJSON.price.total);
        $('.ajax_cart_quantity').removeClass('unvisible');
        $('.ajax_cart_product_txt').removeClass('unvisible');
        $('.ajax_cart_no_product').addClass('unvisible');
      }
      else {
        $('.ajax_cart_no_product').removeClass('unvisible');
        $('.ajax_cart_quantity').addClass('unvisible');
        $('.ajax_cart_product_txt').addClass('unvisible');
      }

    }
  });
}

function display (view) {
  if (view == 'list') {
    $('ul.product_list').removeClass('grid').addClass('list row');
    $('.product_list > li').removeClass('col-xs-12 col-sm-6 col-md-4').addClass('col-xs-12');
    $('.product_list > li').each(function (index, element) {
      html = '';
      html = '<div class="product-container"><div class="row">';
      html += '<div class="left-block col-xs-4 col-xs-5 col-md-4">' + $(element).find('.left-block').html() + '</div>';
      html += '<div class="center-block col-xs-4 col-xs-7 col-md-4">';
      html += '<div class="product-flags">' + $(element).find('.product-flags').html() + '</div>';
      html += '<h5 itemprop="name">' + $(element).find('h5').html() + '</h5>';
      var rating = $(element).find('.comments_note').html(); // check : rating
      if (rating != null) {
        html += '<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating" class="comments_note">' + rating + '</div>';
      }
      html += '<p class="product-desc">' + $(element).find('.product-desc').html() + '</p>';
      var colorList = $(element).find('.color-list-container').html();
      if (colorList != null) {
        html += '<div class="color-list-container">' + colorList + '</div>';
      }
      var availability = $(element).find('.availability').html(); // check : catalog mode is enabled
      if (availability != null) {
        html += '<span class="availability">' + availability + '</span>';
      }
      html += '</div>';
      html += '<div class="right-block col-xs-4 col-xs-12 col-md-4"><div class="right-block-content row">';
      var price = $(element).find('.content_price').html(); // check : catalog mode is enabled
      if (price != null) {
        html += '<div class="content_price col-xs-5 col-md-12">' + price + '</div>';
      }
      html += '<div class="button-container col-xs-7 col-md-12">' + $(element).find('.button-container').html() + '</div>';
      html += '</div>';
      html += '</div></div>';
      $(element).html(html);
    });
    $('.display').find('li#list').addClass('selected');
    $('.display').find('li#grid').removeAttr('class');
    $.totalStorage('display', 'list');
  } else {
    $('ul.product_list').removeClass('list').addClass('grid row');
    $('.product_list > li').removeClass('col-xs-12').addClass('col-xs-12 col-sm-6 col-md-4');
    $('.product_list > li').each(function (index, element) {
      html = '';
      html += '<div class="product-container">';
      html += '<div class="left-block">' + $(element).find('.left-block').html() + '</div>';
      html += '<div class="right-block">';
      html += '<div class="right-block-container">';
      html += '<div class="product-flags">' + $(element).find('.product-flags').html() + '</div>';
      html += '<h5 itemprop="name">' + $(element).find('h5').html() + '</h5>';
      var rating = $(element).find('.comments_note').html(); // check : rating
      if (rating != null) {
        html += '<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating" class="comments_note">' + rating + '</div>';
      }
      html += '<p itemprop="description" class="product-desc">' + $(element).find('.product-desc').html() + '</p>';
      var price = $(element).find('.content_price').html(); // check : catalog mode is enabled
      if (price != null) {
        html += '<div class="content_price">' + price + '</div>';
      }
      html += '</div>';
      html += '<div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="button-container">' + $(element).find('.button-container').html() + '</div>';
      var colorList = $(element).find('.color-list-container').html();
      if (colorList != null) {
        html += '<div class="color-list-container">' + colorList + '</div>';
      }
      var availability = $(element).find('.availability').html(); // check : catalog mode is enabled
      if (availability != null) {
        html += '<span class="availability">' + availability + '</span>';
      }
      html += '</div>';

      html += '</div>';
      $(element).html(html);
    });
    $('.display').find('li#grid').addClass('selected');
    $('.display').find('li#list').removeAttr('class');
  }
}
