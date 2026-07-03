  <head>
      <style>
          * {
              box-sizing: border-box;
          }

          body {
              margin: 0;
              background: #031014;
              font-family: 'Roboto', sans-serif;
              color: #E9F3F4;
          }

          .receipt-page {
              width: 430px;
              min-height: 100vh;
              margin: 0 auto;
              padding: 22px 18px;
              background:
                  radial-gradient(circle at top left, rgba(29,216,232,0.08), transparent 35%),
                  radial-gradient(circle at bottom right, rgba(255,90,20,0.10), transparent 38%),
                  #031014;
          }

          .top-header {
              display: flex;
              justify-content: space-between;
              align-items: center;
          }

          .brand {
              display: flex;
              align-items: center;
              gap: 8px;
          }

          .owl-logo {
              width: 24px;
          }

          .scops-logo {
              width: 72px;
          }

          .receipt-label {
              color: #1DD8E8;
              font-size: 10px;
              font-weight: 700;
          }

          .title-row {
              margin-top: 18px;
              position: relative;
              display: flex;
              justify-content: flex-end;
              align-items: flex-start;
              min-height: 120px;
          }

          .title-center {
              position: absolute;
              left: 50%;
              top: 35px;
              transform: translateX(-50%);
              text-align: center;
              width: 100%;
              max-width: 320px;
          }

          .payment-title {
              width: 190px;
              max-width: 100%;
              display: block;
              margin: 0 auto -10px;
          }

          .title-center p {
              margin: 0 0 20px;
              font-size: 12px;
              color: #9AB0B4;
              text-align: center;
          }

          .orange-line {
              width: 52px;
              height: 4px;
              background: #FF5A14;
              border-radius: 20px;
              margin: 0 auto;
          }

          .receipt-id-card {
              min-width: 76px;
              padding: 10px 12px;
              text-align: center;
              border: 1px solid rgba(154,176,180,0.28);
              border-radius: 14px;
              background: rgba(11,37,43,0.45);
          }

          .receipt-id-card span {
              display: block;
              color: #9AB0B4;
              font-size: 8px;
          }

          .receipt-id-card strong {
              display: block;
              margin-top: 3px;
              color: #FF5A14;
              font-size: 21px;
              font-weight: 800;
          }

          .status-card {
              position: relative;
              margin-top: 16px;
              padding: 20px;
              height: 104px;
              border-radius: 16px;
              border: 1px solid rgba(29,216,232,0.33);
              background: linear-gradient(135deg, rgba(11,37,43,0.97), rgba(6,26,31,0.95));
              display: flex;
              align-items: center;
              gap: 16px;
              overflow: hidden;
          }

          .check-circle {
              width: 54px;
              height: 54px;
              border: 1.8px solid #1DD8E8;
              border-radius: 50%;
              color: #1DD8E8;
              display: flex;
              align-items: center;
              justify-content: center;
              font-size: 26px;
              font-weight: 700;
              flex-shrink: 0;
          }

          .status-content h2 {
              margin: 0;
              color: #1DD8E8;
              font-size: 16px;
              font-weight: 800;
          }

          .status-content p {
              margin: 4px 0 8px;
              color: #E9F3F4;
              font-size: 9px;
          }

          .status-pill {
              display: inline-block;
              padding: 5px 10px;
              border-radius: 999px;
              background: rgba(29,216,232,0.12);
              color: #1DD8E8;
              font-size: 8px;
              font-weight: 600;
          }


          .details-card {
              margin-top: 12px;
              padding: 16px;
              border-radius: 14px;
              border: 1px solid rgba(29,216,232,0.23);
              background: linear-gradient(145deg, rgba(11,37,43,0.95), rgba(6,26,31,0.96));
          }

          .details-card h3 {
              margin: 0;
              color: #E9F3F4;
              font-size: 12px;
              font-weight: 800;
              display: flex;
              align-items: center;
              gap: 8px;
          }

          .details-card h3 span {
              color: #FF5A14;
          }

          .details-line {
              height: 1px;
              background: rgba(255,90,20,0.65);
              margin: 12px 0 6px;
          }

          .row {
              display: flex;
              justify-content: space-between;
              align-items: center;
              gap: 14px;
              min-height: 28px;
              border-bottom: 1px solid rgba(233,243,244,0.055);
          }

          .row.last {
              border-bottom: none;
          }

          .label {
              color: #9AB0B4;
              font-size: 9.5px;
          }

          .value {
              color: #E9F3F4;
              font-size: 9.5px;
              font-weight: 500;
              text-align: right;
              max-width: 230px;
              word-break: break-word;
          }

          .orange {
              color: #FF5A14;
          }

          .cyan {
              color: #1DD8E8;
              font-weight: 800;
          }

          .gateway {
              font-size: 8.6px;
          }

          .label {
              display: flex;
              align-items: center;
              gap: 8px;
              color: #9AB0B4;
              font-size: 9.5px;
          }

          .svg-icon,
          .section-icon {
              width: 17px;
              height: 17px;
              border-radius: 6px;
              background: rgba(29, 216, 232, 0.08);
              border: 1px solid rgba(29, 216, 232, 0.2);
              display: inline-flex;
              align-items: center;
              justify-content: center;
              flex-shrink: 0;
          }

          .section-icon {
              background: rgba(255, 90, 20, 0.1);
              border-color: rgba(255, 90, 20, 0.35);
          }

          .svg-icon,
          .section-icon {
              width: 16px;
              height: 16px;
              display: inline-flex;
              align-items: center;
              justify-content: center;
              flex-shrink: 0;
              background: transparent;
              border: none;
          }

          .svg-icon svg,
          .section-icon svg {
              width: 14px;
              height: 14px;
              fill: none;
              stroke-width: 1.9;
              stroke-linecap: round;
              stroke-linejoin: round;
          }

          .svg-icon svg {
              stroke: #1DD8E8;
              opacity: 0.95;
          }

          .section-icon svg {
              stroke: #FF5A14;
          }
          .label {
              display: flex;
              align-items: center;
              gap: 7px;
              color: #9AB0B4;
              font-size: 10px;
              font-weight: 400;
          }
          .receipt-widget {
              min-width: 96px;
              padding: 10px 12px;
              border: 1px solid rgba(29, 216, 232, 0.28);
              border-radius: 16px;
              background: rgba(11, 37, 43, 0.45);
              text-align: center;
          }

          .receipt-widget-top {
              color: #1DD8E8;
              font-size: 9px;
              font-weight: 800;
              letter-spacing: 0.5px;
              margin-bottom: 8px;
          }

          .receipt-widget-id span {
              display: block;
              color: #9AB0B4;
              font-size: 8px;
              text-transform: uppercase;
              margin-bottom: 2px;
          }

          .receipt-widget-id b {
              color: #FF5A14;
              font-size: 22px;
              font-weight: 800;
          }
          .receipt-footer {
              margin-top: 30px;
              display: flex;
              align-items: center;
              justify-content: center;
              gap: 14px;
          }

          .footer-line {
              width: 50px;
              height: 1px;
              background: rgba(255, 90, 20, 0.28);
          }

          .footer-brand {
              display: flex;
              align-items: center;
              gap: 7px;
              padding: 0 6px;
          }

          .footer-owl {
              width: 18px;
              height: 18px;
              opacity: 0.95;
          }

          .footer-logo {
              width: 58px;
              opacity: 0.9;
          }
          .status-failed {
              color: #FF4D4D;
          }
          .failed-circle {
              border-color: #FF4D4D;
              color: #FF4D4D;
              box-shadow: 0 0 18px rgba(255, 77, 77, 0.15);
          }
      </style>
      <title></title>
  </head>

  <body>
    <main class="receipt-page">
      <header class="top-header">
        <div class="brand">
          <img src="{{ asset('asset/owl_cyan.png') }}" class="owl-logo" alt="" />
          <img src="{{ asset('asset/scops_white.png') }}" class="scops-logo" alt="" />
        </div>

        <div class="receipt-label">▤ RECEIPT</div>
      </header>

      <section class="title-row">
        <div class="title-center">
          <img src="{{ asset('asset/payment_receipt.png') }}" class="payment-title" alt=""/>
          <p>Your payment was processed successfully.</p>
          <div class="orange-line"></div>
        </div>

        <div class="receipt-id-card">
          <span>RECEIPT ID</span>
          <strong>#{{ $payment->payment_id }}</strong>
        </div>
      </section>

      <section class="status-card">
        <div
          class="check-circle {{ $payment->status === 'failed' ? 'failed-circle' : '' }}"
        >
          {{ $payment->status === 'successful' ? '✓' : '✕' }}
        </div>
        <div class="status-content">
          <h2
            class="{{ $payment->status === 'failed' ? 'status-failed' : 'cyan' }}"
          >
            {{ strtoupper($payment->status) }}
          </h2>

          <p>
            {{ $payment->status === 'successful' ? 'Your payment has been
            completed.' : 'Your payment could not be completed.' }}
          </p>

          <span class="status-pill">
            ● {{ $payment->status === 'successful' ? 'Payment Completed' :
            'Payment Failed' }}
          </span>
        </div>
      </section>

      <section class="details-card">
        <h3>
          <span class="section-icon">
            <svg viewBox="0 0 24 24">
              <path
                d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
              />
              <path d="M14 2v6h6" />
              <path d="M8 13h8" />
              <path d="M8 17h5" />
            </svg>
          </span>
          PAYMENT DETAILS
        </h3>

        <div class="details-line"></div>

        <div class="row">
          <span class="label">Payment ID</span>
          <span class="value orange">{{ $payment->payment_id }}</span>
        </div>

        <div class="row">
          <span class="label">User</span>
          <span class="value"
            >{{ trim($user->first_name . ' ' . $user->last_name) }}</span
          >
        </div>

        <div class="row">
          <span class="label">Email</span>
          <span class="value">{{ $user->email }}</span>
        </div>

        <div class="row">
          <span class="label">Subscription</span>
          <span class="value">{{ $subscription->service->name ?? 'N/A' }}</span>
        </div>

        <div class="row">
          <span class="label">Amount</span>
          <span class="value orange"> {{ $payment->amount }} {{ $payment->currency ??'JOD' }} </span>
        </div>
        <div class="row">
          <span class="label">Status</span>
          <span
            class="
          value
          {{ $payment->status === 'successful' ? 'cyan' : '' }}
          {{ $payment->status === 'failed' ? 'status-failed' : '' }}
        "
          >
            {{ strtoupper($payment->status) }}
          </span>
        </div>


        <div class="row last">
          <span class="label">Attempted On</span>
          <span class="value">{{ $payment->attempted_on  }}</span>
        </div>

      </section>
        <section class="details-card">
            <h3>
          <span class="section-icon">
            <svg viewBox="0 0 24 24">
              <path
                  d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
              />
              <path d="M14 2v6h6" />
              <path d="M8 13h8" />
              <path d="M8 17h5" />
            </svg>
          </span>
                Card Information
            </h3>

            <div class="details-line"></div>

            <div class="row">
                <span class="label">Card ID </span>
                <span class="value orange">{{ $card->card_id ?? 'N/A' }}</span>
            </div>
            <div class="row">
                <span class="label">Card Brand </span>
                <span class="value">{{ $card->card_brand ?? 'N/A' }}</span>
            </div>

            <div class="row last">
                <span class="label">Last 4 digits </span>
                <span class="value">{{ $card ? '**** **** **** ' . $card->last4 : 'N/A' }}</span>
            </div>

        </section>


      <footer class="receipt-footer">
        <span class="footer-line"></span>

        <div class="footer-brand">
            <img src="{{ asset('asset/owl_cyan.png') }}" class="owl-logo"  alt=""/>
            <img src="{{ asset('asset/scops_white.png') }}" class="scops-logo"  alt=""/>
        </div>

        <span class="footer-line"></span>
      </footer>
    </main>
  </body>
