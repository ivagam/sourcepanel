<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en" data-theme="light">

<x-head />

<body>

    <!-- ..::  header area start ::.. -->
    <x-sidebar />
    <!-- ..::  header area end ::.. -->

    <main class="dashboard-main">

        <!-- ..::  navbar start ::.. -->
        <x-navbar />
        <!-- ..::  navbar end ::.. -->
        <div class="dashboard-main-body">
            
            <!-- ..::  breadcrumb  start ::.. -->
            <x-breadcrumb title='{{ isset($title) ? $title : "" }}' subTitle='{{ isset($subTitle) ? $subTitle : "" }}' />
            <!-- ..::  header area end ::.. -->

            @yield('content')
        
        </div>
        <!-- ..::  footer  start ::.. -->
        <x-footer />
        <!-- ..::  footer area end ::.. -->

    </main>

    <!-- ..::  scripts  start ::.. -->
    <x-script  script='{!! isset($script) ? $script : "" !!}' />
    <!-- ..::  scripts  end ::.. -->

    <script>
        $(document).ready(function() {
            $("#add-item").click(function() {
                var clone = $(".item-block").first().clone(); // clone the first item
                clone.find('input').val('');                  // clear all input fields
                clone.find('select').val('');                  // reset select dropdowns
                $("#item-wrapper").append(clone);              // append the cloned item
            });

            $(document).on("click", ".remove-item", function() {
                // Only remove if there are more than one item-blocks
                if ($('.item-block').length > 1) {
                    $(this).closest('.item-block').remove();
                } else {
                    alert("At least one item is required!");
                }
            });
        });
function reloadPage(categoryId) {
    // Get the current URL
    let url = new URL(window.location.href);
    // Update or add the 'category_id' query parameter in the URL
    url.searchParams.set('indent_id', categoryId);
    // Reload the page with the updated URL
    window.location.href = url;
}

function removeItem(id,obj){
      // Only remove if there are more than one item-blocks
      if ($('.item-block'+id).length > 1) {
            $(obj).closest('.item-block'+id).remove();
        } else {
            alert("At least one item is required!");
        }
}
function addItem(id){
    var clone = $(".item-block"+id).first().clone(); // clone the first item
    $("#item-wrapper"+id).append(clone);              // append the cloned item
}

function reloadIndentPage(categoryId) {
    // Get the current URL
    let url = new URL(window.location.href);
    // Update or add the 'category_id' query parameter in the URL
    if(categoryId != ""){
        url.searchParams.set('customer_id', categoryId);
    }
   
    // Reload the page with the updated URL
    window.location.href = url;
}

function updateIndentIdInUrl(indentId) {
    const url = new URL(window.location.href);
    url.searchParams.set('indent_id', indentId); // Set or update the indent_id
    window.location.href = url.toString(); // reloads the page with the updated URL
}
function removeIndentIdFromUrl() {
    const url = new URL(window.location.href);
    url.searchParams.delete('indent_id');
    window.history.pushState({}, '', url);
    //window.location.href = url.toString(); // reloads the page with updated URL
}
function fetchIndentId(customerId) {
    const BASE_URL = "{{ config('app.url') }}";

    fetch(`${BASE_URL}/indent/getIndentId?customer_id=${customerId}`)
        .then(response => response.json())
        .then(data => {
            if (data.indent_id) {
                console.log("Indent ID:", data.indent_id);
                $("indent_id").val(data.indent_id);
                updateIndentIdInUrl(data.indent_id);
            }else{
                removeIndentIdFromUrl();
                $("#indent_id").val("");
                $("#indent_list").hide();
            }
        })
        .catch(error => {
            console.error('Error fetching indent ID:', error);
        });
}

function updatePurchasedInUrl(purchaseId) {
    const url = new URL(window.location.href);
    url.searchParams.set('purchase_id', purchaseId); // Set or update the indent_id
    window.location.href = url.toString(); // reloads the page with the updated URL
}
function removePurchaseIdFromUrl() {
    const url = new URL(window.location.href);
    url.searchParams.delete('purchase_id');
    window.history.pushState({}, '', url);
    //window.location.href = url.toString(); // reloads the page with updated URL
}
function fetchPurchaseId(vendorId) {
    const BASE_URL = "{{ config('app.url') }}";

    fetch(`${BASE_URL}/purchase/getPurchaseId?vendor_id=${vendorId}`)
        .then(response => response.json())
        .then(data => {
            if (data.purchase_id) {
                console.log("Indent ID:", data.purchase_id);
                $("purchase_id").val(data.purchase_id);
                updatePurchasedInUrl(data.purchase_id);
            }else{
                removePurchaseIdFromUrl();
                $("#purchase_id").val("");
                $("#purchase_list").hide();
            }
        })
        .catch(error => {
            console.error('Error fetching purchase ID:', error);
        });
}

function updateSalesInUrl(salesId,indentId) {
    const url = new URL(window.location.href);
    url.searchParams.set('sales_id', salesId); // Set or update the indent_id
    url.searchParams.set('indent_id', indentId); // Set or update the indent_id
    window.location.href = url.toString(); // reloads the page with the updated URL
}
function removeSalesIdFromUrl() {
    const url = new URL(window.location.href);
    url.searchParams.delete('sales_id');
    window.history.pushState({}, '', url);
    //window.location.href = url.toString(); // reloads the page with updated URL
}
function fetchSalesId(indentId) {
    const BASE_URL = "{{ config('app.url') }}";

    fetch(`${BASE_URL}/sales/getSalesId?indent_id=${indentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.sales_id) {
                console.log("Sales ID:", data.sales_id);
                $("#sales_id").val(data.sales_id);
                updateSalesInUrl(data.sales_id,data.indent_id);
            }else{
                updateSalesInUrl(0,indentId);
                removeSalesIdFromUrl();
                $("#sales_id").val("");
                $("#sales_item").hide();
                $('#indent_list').hide();
            }
        })
        .catch(error => {
            console.error('Error fetching sales ID:', error);
        });
}
function validateDispatch()
{
    $total_indent = $("#total_indent").val();
    $total_sales = $("#total_sales").val();
    $reason = $('#reason').val();
    if($total_indent != $total_sales && $reason == ''){
        alert("the indent quantity not match with sales, so please provide the reason");
        return false;
    }
    return true;
}

 $(document).ready(function () {
        $('.select2').select2({
            placeholder: "Select Domains",
            closeOnSelect: false,
            allowClear: true,
            width: '100%'
        });
});


    </script>
</body>

</html>