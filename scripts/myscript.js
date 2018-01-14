$(document).ready(function () {
    $('img').addClass('img-responsive');
    $('#datatable').DataTable();
});

function collapseNavbar() {
    if ($(".navbar").offset().top > 30) {
        $(".navbar-fixed-top").addClass("top-nav-collapse");
    } else {
        $(".navbar-fixed-top").removeClass("top-nav-collapse");
    }
}

$(window).scroll(collapseNavbar);
$(document).ready(collapseNavbar);