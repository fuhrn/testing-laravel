{{--<h1>{{ $concert->title }}</h1>--}}
{{--<h2>{{ $concert->subtitle }}</h2>--}}
{{--<p>{{ $concert->formatted_date }}</p>--}}
{{--<p>Doors at {{ $concert->formatted_start_time }}</p>--}}
{{--<p>{{ $concert->ticket_price_in_dollars }}</p>--}}
{{--<p>{{ $concert->venue }}</p>--}}
{{--<p>{{ $concert->venue_address }}</p>--}}
{{--<p>{{ $concert->city }}, {{ $concert->state }} {{ $concert->zip }}</p>--}}
{{--<p>{{ $concert->additional_information }}</p>--}}

@extends('layouts.master')

@section('body')
    <div class="bg-soft p-xs-y-7 full-height">
        <div class="container">
            <div class="row m-xs-b-5">
                <div class="col col-md-6 col-md-offset-3 m-xs-b-4 m-lg-b-0">
                    <div class="card">
                        <div class="card-section">
                            <div class="m-xs-b-5">
                                <h1 class="wt-bold text-ellipsis">{{ $concert->title }}</h1>
                                <span class="wt-medium text-ellipsis">{{ $concert->subtitle }}</span>
                            </div>
                            <div class="m-xs-b-5">
                                <div class="media-object">
                                    <div class="media-body p-xs-l-2">
                                        <span>
                                            <i class="fa fa-calendar-o" aria-hidden="true"></i>
                                        </span>
                                        <span class="wt-medium">{{ $concert->formatted_date }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="m-xs-b-5">
                                <div class="media-object">
                                    <div class="media-body p-xs-l-2">
                                        <span><i class="fa fa-clock-o" aria-hidden="true"></i></span>
                                        <span
                                            class="wt-medium block">Doors at {{ $concert->formatted_start_time }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="m-xs-b-5">
                                <div class="media-object">
                                    <div class="media-body p-xs-l-2">
                                        <span><i class="fa fa-usd" aria-hidden="true"></i></span>
                                        <span class="wt-medium block">{{ $concert->ticket_price_in_dollars }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-dark-soft m-xs-b-5">
                                <div class="media-object">
                                    <div class="media-body p-xs-l-2">
                                        <span><i class="fa fa-location-arrow" aria-hidden="true"></i></span>
                                        <h3 class="text-base wt-medium text-dark">{{ $concert->venue }}</h3>
                                        {{ $concert->venue_address }}<br>
                                        {{ $concert->city }}, {{ $concert->state }} {{ $concert->zip }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-dark-soft">
                                <div class="media-object">
                                    <div class="media-body p-xs-l-2">
                                        <span><i class="fa fa-info-circle" aria-hidden="true"></i></span>
                                        <h3 class="text-base wt-medium text-dark">Additional Information</h3>
                                        <p>{{ $concert->additional_information}}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center text-dark-soft wt-medium">
                                <p>Powered by TicketBeast</p>
                            </div>
                        </div>
{{--                        <div class="border-t">--}}
{{--                            <div class="card-section">--}}
{{--                                <ticket-checkout--}}
{{--                                    :concert-id="{{ $concert->id }}"--}}
{{--                                    concert-title="{{ $concert->title }}"--}}
{{--                                    :price="{{ $concert->ticket_price }}"--}}
{{--                                ></ticket-checkout>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="container">
        <div class="col-md-6 col-md-offset-3">
            <h1>Payment Form</h1>
            <div class="spacer"></div>

            @if (session()->has('success_message'))
                <div class="alert alert-success">
                    {{ session()->get('success_message') }}
                </div>
            @endif

            @if(count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ url('/concerts/'.$concert->id.'/orders') }}" method="POST" id="payment-form">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="name_on_card">Name on Card</label>
                    <input type="text" class="form-control" id="name_on_card" name="name_on_card">
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" class="form-control" name="email">
                </div>

                <div class="form-group">
                    <label for="ticket_quantity">Ticket Quantity</label>
                    <input type="number" step="1" class="form-control" name="ticket_quantity">
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" class="form-control" id="address" name="address">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" class="form-control" id="city" name="city">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="province">Province</label>
                            <input type="text" class="form-control" id="province" name="province">
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="postalcode">Postal Code</label>
                            <input type="text" class="form-control" id="postalcode" name="postalcode">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="country">Country</label>
                            <input type="text" class="form-control" id="country" name="country">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                        </div>
                    </div>

                </div>
                <div class="form-group">
                    <label for="card-element">Credit Card</label>
                    <div id="card-element">
                        <!-- a Stripe Element will be inserted here. -->
                    </div>

                    <!-- Used to display form errors -->
                    <div id="card-errors" role="alert"></div>
                </div>

                <div class="spacer"></div>

                <button type="submit" class="btn btn-success">Submit Payment</button>
            </form>
        </div>
    </div>

    <script>
        (function(){
            // Create a Stripe client
            var stripe = Stripe('{{ config('services.stripe.key') }}');
            // Create an instance of Elements
            var elements = stripe.elements();
            // Custom styling can be passed to options when creating an Element.
            // (Note that this demo uses a wider set of styles than the guide below.)
            var style = {
                base: {
                    color: '#32325d',
                    lineHeight: '18px',
                    fontFamily: '"Raleway", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            };
            // Create an instance of the card Element
            var card = elements.create('card', {
                style: style,
                hidePostalCode: true
            });
            // Add an instance of the card Element into the `card-element` <div>
            card.mount('#card-element');
            // Handle real-time validation errors from the card Element.
            card.addEventListener('change', function(event) {
                var displayError = document.getElementById('card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });
            // Handle form submission
            var form = document.getElementById('payment-form');
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                var options = {
                    name: document.getElementById('name_on_card').value,
                }
                stripe.createToken(card, options).
                then(function(result) {
                    if (result.error) {
                        // Inform the user if there was an error
                        var errorElement = document.getElementById('card-errors');
                        errorElement.textContent = result.error.message;
                    } else {
                        // Send the token to your server
                        stripeTokenHandler(result.token);
                    }
                });  //y si ponemos el redirect desde aqui?
            });
            function stripeTokenHandler(token) {
                // Insert the token ID into the form so it gets submitted to the server
                var form = document.getElementById('payment-form');
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                // hiddenInput.setAttribute('name', 'stripeToken'); cambien el nombre del campo
                hiddenInput.setAttribute('name', 'payment_token');
                hiddenInput.setAttribute('value', token.id);
                form.appendChild(hiddenInput);
                // Submit the form
                form.submit();
            }
        })();
    </script>
@endsection


