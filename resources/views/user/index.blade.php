@extends('layouts.app')

@section('content')

<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
      <h2 class="page-title">My Account</h2>
      <div class="row">
        <div class="col-lg-3">
          <ul class="account-nav">
            <li><a href="my-account.html" class="menu-link menu-link_us-s">Dashboard</a></li>
            <li><a href="account-orders.html" class="menu-link menu-link_us-s">Orders</a></li>
            <li><a href="account-address.html" class="menu-link menu-link_us-s">Addresses</a></li>
            <li><a href="account-details.html" class="menu-link menu-link_us-s">Account Details</a></li>
            <li><a href="account-wishlist.html" class="menu-link menu-link_us-s">Wishlist</a></li>
            <li>
                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                    @csrf   
                    <a href="#" id="logout-link" class="menu-link menu-link_us-s">Logout</a>
                </form>
            </li>
          </ul>
        </div>
        <div class="col-lg-9">
          <div class="page-content my-account__dashboard">
            <p>Hello <strong>User</strong></p>
            <p>From your account dashboard you can view your <a class="unerline-link" href="account_orders.html">recent
                orders</a>, manage your <a class="unerline-link" href="account_edit_address.html">shipping
                addresses</a>, and <a class="unerline-link" href="account_edit.html">edit your password and account details</a>.</p>
          </div>
        </div>
      </div>
    </section>
</main>

<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/sweetalert.min.js') }}"></script>
<script>
    document.getElementById('logout-link').addEventListener('click', function(event) {
        event.preventDefault();
        swal({
            title: "Are you sure?",
            text: "Are you sure you want to log out?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willLogout) => {
            if (willLogout) {
                document.getElementById('logout-form').submit();
            }
        });
    });
</script>

@endsection