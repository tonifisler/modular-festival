// You will use that file to import all your scripts
// Ex: import gallery from './gallery';

/* eslint-disable */
(function(){
  $(document).on('ready', function() {
    const $body = $('body');
    const $logo = $('.navbar-brand');
    const baseHeight = 133;

    // scrollspy for menu
    $body.scrollspy({ target: '#main-navbar' });

    // shrink menu size on scroll
    let isSmallMenu = false;
    $(document).on('scroll', function() {
      window.requestAnimationFrame(function(){
        const positionTop = $body.scrollTop();
        if (positionTop > baseHeight && !isSmallMenu) {
          toggleMenuSize();
        } else if(positionTop < baseHeight && isSmallMenu) {
          toggleMenuSize();
        }
      });
    });

    const toggleMenuSize = function() {
      $logo.toggleClass('scrolled');
      isSmallMenu = !isSmallMenu;
    };
  });
}());
/* eslint-enable */
