<nav class="sidenav shadow-right sidenav-light">
    <div class="sidenav-menu">
        <div class="nav accordion" id="accordionSidenav">
            <!-- Sidenav Menu Heading (Core)-->
            <div class="sidenav-menu-heading">Core</div>
            <a class="nav-link {{ Request::is('dashboard*') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <div class="nav-link-icon"><i data-feather="activity"></i></div>
                Dashboard
            </a>

            @if(auth()->user()->role == "admin")
                {{-- Admin User --}}
                <!-- Sidenav Accordion (Pages)-->
                <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse"
                   data-bs-target="#orderPages" aria-expanded="false" aria-controls="orderPages">
                    <div class="nav-link-icon"><i data-feather="grid"></i></div>
                    Reports
                    <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="orderPages" data-bs-parent="#accordionSidenav">
                    <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPagesCompletes">
                        <!-- Nested Sidenav Accordion (Pages -> Orders)-->
                        <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse"
                           data-bs-target="#pagesCollapseCompletes" aria-expanded="false"
                           aria-controls="pagesCollapseCompletes">
                            Completes Orders
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="pagesCollapseCompletes"
                             data-bs-parent="#accordionSidenavPagesCompletes">
                            <nav class="sidenav-menu-nested nav">
                                <a class="nav-link {{ Request::is('orders/report*') ? 'active' : '' }}"
                                   href="{{ route('orders.dailyOrderReport') }}">
                                    <div class="nav-link-icon"><i class="fa-solid fa-flag"></i></div>
                                    Daily Order Report
                                </a>
                                <a class="nav-link {{ Request::is('orders/week*') ? 'active' : '' }}"
                                   href="{{ route('orders.currentWeekOrders') }}">
                                    <div class="nav-link-icon"><i class="fa-solid fa-calendar-week"></i></div>
                                    Current Week Orders
                                </a>
                                <a class="nav-link {{ Request::is('orders/month*') ? 'active' : '' }}"
                                   href="{{ route('orders.currentMonthOrders') }}">
                                    <div class="nav-link-icon"><i class="fa-solid fa-moon"></i></div>
                                    Current Month Orders
                                </a>
                                <a class="nav-link {{ Request::is('orders/year*') ? 'active' : '' }}"
                                   href="{{ route('orders.currentYearOrders') }}">
                                    <div class="nav-link-icon"><i class="fa-solid fa-sun"></i></div>
                                    Current Year Orders
                                </a>
                            </nav>
                        </div>
                        <a class="nav-link {{ Request::is('orders/due*') ? 'active' : '' }}"
                           href="{{ route('order.dueOrders') }}">
                            <div class="nav-link-icon"><i class="fa-solid fa-moon"></i></div>
                            Due Orders (Unpaid orders)
                        </a>
                        <a class="nav-link {{ Request::is('orders') ? 'active' : '' }}"
                           href="{{ route('orders.allOrders') }}">
                            <div class="nav-link-icon"><i class="fa-solid fa-moon"></i></div>
                            All Orders
                        </a>
                        <!-- Nested Sidenav Accordion (Pages -> People)-->
                        <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse"
                           data-bs-target="#pagesCollapseCashflow" aria-expanded="false"
                           aria-controls="pagesCollapseCashflow">
                            Cashflow Reports
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="pagesCollapseCashflow"
                             data-bs-parent="#accordionSidenavPagesCashflow">
                            <nav class="sidenav-menu-nested nav">
                                <a class="nav-link {{ Request::is('orders/report/cash*') ? 'active' : '' }}"
                                   href="{{ route('orders.dailyCashReport') }}">
                                    <div class="nav-link-icon"><i class="fa-solid fa-flag"></i></div>
                                    Daily Cash Report
                                </a>
                                <a class="nav-link {{ Request::is('orders/report/cash/current-week*') ? 'active' : '' }}"
                                   href="{{ route('orders.currentWeekCashReport') }}">
                                    <div class="nav-link-icon"><i class="fa-solid fa-calendar-week"></i></div>
                                    Current Week Cash
                                </a>
                                <a class="nav-link {{ Request::is('orders/report/cash/current-month*') ? 'active' : '' }}"
                                   href="{{ route('orders.currentMonthCashReport') }}">
                                    <div class="nav-link-icon"><i class="fa-solid fa-moon"></i></div>
                                    Current Month Cash
                                </a>
                                <a class="nav-link {{ Request::is('orders/report/cash/current-year*') ? 'active' : '' }}"
                                   href="{{ route('orders.currentYearCashReport') }}">
                                    <div class="nav-link-icon"><i class="fa-solid fa-sun"></i></div>
                                    Current Year Cash
                                </a>
                            </nav>
                        </div>

                        <a class="nav-link {{ Request::is('stocks*') ? 'active' : '' }}"
                           href="{{ route('stocks.index') }}">
                            <div class="nav-link-icon"><i class="fa-solid fa-circle-check"></i></div>
                            Stocks
                        </a>

                        <a class="nav-link {{ Request::is('journals*') ? 'active' : '' }}"
                           href="{{ route('journals.index') }}">
                            <div class="nav-link-icon"><i class="fa-solid fa-circle-check"></i></div>
                            Journals
                        </a>
                    </nav>
                </div>
                <!-- Sidenav Accordion (Pages -> Purchases)-->
                <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse"
                   data-bs-target="#purchasePages" aria-expanded="false" aria-controls="purchasePages">
                    <div class="nav-link-icon"><i data-feather="grid"></i></div>
                    Purchases
                    <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>

                <div class="collapse" id="purchasePages" data-bs-parent="#accordionSidenav">
                    <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPagesPurchases">
                        <!-- Nested Sidenav Accordion (Pages -> Purchase)-->
                        <a class="nav-link {{ Request::is('purchases', 'purchase/create*', 'purchases/details*') ? 'active' : '' }}"
                           href="{{ route('purchases.allPurchases') }}">
                            <div class="nav-link-icon"><i class="fa-solid fa-cash-register"></i></div>
                            All
                        </a>
                        <a class="nav-link {{ Request::is('purchases/report*') ? 'active' : '' }}"
                           href="{{ route('purchases.dailyPurchaseReport') }}">
                            <div class="nav-link-icon"><i class="fa-solid fa-flag"></i></div>
                            Daily Purchase Report
                        </a>
                    </nav>
                </div>
                <!-- Sidenav Accordion (Pages -> Expenses)-->
                <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse"
                   data-bs-target="#expensePages" aria-expanded="false" aria-controls="expensePages">
                    <div class="nav-link-icon"><i data-feather="grid"></i></div>
                    Expenses
                    <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>

                <div class="collapse" id="expensePages" data-bs-parent="#accordionSidenav">
                    <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPagesExpenses">
                        <!-- Nested Sidenav Accordion (Pages -> Expense)-->
                        <a class="nav-link {{ Request::is('expenses/pending*') ? 'active' : '' }}"
                           href="{{ route('expenses.pendingExpenses') }}">Pending</a>
                        <a class="nav-link {{ Request::is('expenses/approved*') ? 'active' : '' }}"
                           href="{{ route('expenses.approvedExpenses') }}">Approved</a>
                        <a class="nav-link {{ Request::is('expenses') ? 'active' : '' }}"
                           href="{{ route('expenses.allExpenses') }}">All</a>
                    </nav>
                </div>
                <!-- Sidenav Accordion (Pages -> Settings)-->
                <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse"
                   data-bs-target="#settingsPages" aria-expanded="false" aria-controls="settingsPages">
                    <div class="nav-link-icon"><i data-feather="grid"></i></div>
                    Settings
                    <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>

                <div class="collapse" id="settingsPages" data-bs-parent="#accordionSidenav">
                    <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPagesSettings">
                        <!-- Nested Sidenav Accordion (Pages -> Settings)-->
                        <a class="nav-link {{ Request::is('products*') ? 'active' : '' }}"
                           href="{{ route('products.index') }}">
                            <div class="nav-link-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
                            Products
                        </a>
                        <a class="nav-link {{ Request::is('categories*') ? 'active' : '' }}"
                           href="{{ route('categories.index') }}">
                            <div class="nav-link-icon"><i class="fa-solid fa-folder"></i></div>
                            Categories
                        </a>
                        <a class="nav-link {{ Request::is('units*') ? 'active' : '' }}"
                           href="{{ route('units.index') }}">
                            <div class="nav-link-icon"><i class="fa-solid fa-folder"></i></div>
                            Units
                        </a>
                        <a class="nav-link {{ Request::is('methods*') ? 'active' : '' }}"
                           href="{{ route('methods.index') }}">
                            <div class="nav-link-icon"><i class="fa-solid fa-folder"></i></div>
                            Methods
                        </a>
                        <a class="nav-link {{ Request::is('departments*') ? 'active' : '' }}"
                           href="{{ route('departments.index') }}">
                            <div class="nav-link-icon"><i class="fa-solid fa-folder"></i></div>
                            Departments
                        </a>
                        <a class="nav-link {{ Request::is('banks*') ? 'active' : '' }}"
                           href="{{ route('banks.index') }}">
                            <div class="nav-link-icon"><i class="fa-solid fa-folder"></i></div>
                            Banks
                        </a>
                    </nav>
                </div>

                <!-- Sidenav Accordion (Pages -> People)-->
                <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse"
                   data-bs-target="#peoplePages" aria-expanded="false" aria-controls="peoplePages">
                    <div class="nav-link-icon"><i data-feather="grid"></i></div>
                    People
                    <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>

                <div class="collapse" id="peoplePages" data-bs-parent="#accordionSidenav">
                    <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPagesPeople">
                        <!-- Nested Sidenav Accordion (Pages -> People)-->
                        <a class="nav-link {{ Request::is('users*') ? 'active' : '' }}"
                           href="{{ route('users.index') }}">
                            <div class="nav-link-icon"><i class="fa-solid fa-users"></i></div>
                            Users
                        </a>
                        <a class="nav-link {{ Request::is('customers*') ? 'active' : '' }}"
                           href="{{ route('customers.index') }}">
                            <div class="nav-link-icon"><i class="fa-solid fa-users"></i></div>
                            Customers
                        </a>
                        <a class="nav-link {{ Request::is('suppliers*') ? 'active' : '' }}"
                           href="{{ route('suppliers.index') }}">
                            <div class="nav-link-icon"><i class="fa-solid fa-users"></i></div>
                            Suppliers
                        </a>
                    </nav>
                </div>
            @elseif(auth()->user()->role == "seller")
                {{-- Seller User --}}
                <a class="nav-link {{ Request::is('pos*') ? 'active' : '' }}" href="{{ route('pos.index') }}">
                    <div class="nav-link-icon"><i class="fa-solid fa-cart-shopping"></i></div>
                    POS
                </a>
                <!-- Sidenav Heading (Orders Complete)-->
                <div class="sidenav-menu-heading">Complete Orders</div>
                <a class="nav-link {{ Request::is('orders/complete*') ? 'active' : '' }}"
                   href="{{ route('order.completeOrders') }}">
                    <div class="nav-link-icon"><i class="fa-solid fa-circle-check"></i></div>
                    Complete Orders
                </a>
                <!-- Sidenav Heading (Orders Pending)-->
                <div class="sidenav-menu-heading">Pending Orders</div>
                <a class="nav-link {{ Request::is('orders/pending*') ? 'active' : '' }}"
                   href="{{ route('order.pendingOrders') }}">
                    <div class="nav-link-icon"><i class="fa-solid fa-circle-check"></i></div>
                    Pending Orders
                </a>
                <!-- Sidenav Heading (Orders Due)-->
                <div class="sidenav-menu-heading">Due Orders</div>
                <a class="nav-link {{ Request::is('orders/due*') ? 'active' : '' }}"
                   href="{{ route('order.dueOrders') }}">
                    <div class="nav-link-icon"><i class="fa-solid fa-circle-check"></i></div>
                    Pay Due Orders
                </a>
                <!-- Sidenav Heading (Orders Due)-->
                <div class="sidenav-menu-heading">Refund Orders</div>
                <a class="nav-link {{ Request::is('orders/refund*') ? 'active' : '' }}"
                   href="{{ route('order.refundOrders') }}">
                    <div class="nav-link-icon"><i class="fa-solid fa-circle-check"></i></div>
                    Pay-back Refund Orders
                </a>

                <a class="nav-link {{ Request::is('expenses*') ? 'active' : '' }}"
                   href="{{ route('expenses.allExpenses') }}">
                    <div class="nav-link-icon"><i class="fa-solid fa-folder"></i></div>
                    Expenses
                </a>
                <a class="nav-link {{ Request::is('deposits*') ? 'active' : '' }}" href="{{ route('deposits.index') }}">
                    <div class="nav-link-icon"><i class="fa-solid fa-folder"></i></div>
                    Deposits
                </a>
                <a class="nav-link {{ Request::is('issues*') ? 'active' : '' }}" href="{{ route('issues.index') }}">
                    <div class="nav-link-icon"><i class="fa-solid fa-folder"></i></div>
                    Issues
                </a>

                <a class="nav-link {{ Request::is('customers*') ? 'active' : '' }}"
                   href="{{ route('customers.index') }}">
                    <div class="nav-link-icon"><i class="fa-solid fa-users"></i></div>
                    Customers
                </a>


                <!-- Sidenav Accordion (Pages)-->
                <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse"
                   data-bs-target="#orderPages" aria-expanded="false" aria-controls="orderPages">
                    <div class="nav-link-icon"><i data-feather="grid"></i></div>
                    Reports
                    <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="orderPages" data-bs-parent="#accordionSidenav">
                    <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPagesCompletes">
                        <!-- Nested Sidenav Accordion (Pages -> Orders)-->
                        <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse"
                           data-bs-target="#pagesCollapseCompletes" aria-expanded="false"
                           aria-controls="pagesCollapseCompletes">
                            Completes Orders
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="pagesCollapseCompletes"
                             data-bs-parent="#accordionSidenavPagesCompletes">
                            <nav class="sidenav-menu-nested nav">
                                <a class="nav-link {{ Request::is('orders/report*') ? 'active' : '' }}"
                                   href="{{ route('orders.dailyOrderReport') }}">
                                    <div class="nav-link-icon"><i class="fa-solid fa-flag"></i></div>
                                    Daily Order Report
                                </a>
                                <a class="nav-link {{ Request::is('orders/week*') ? 'active' : '' }}"
                                   href="{{ route('orders.currentWeekOrders') }}">
                                    <div class="nav-link-icon"><i class="fa-solid fa-calendar-week"></i></div>
                                    Current Week Orders
                                </a>
                                <a class="nav-link {{ Request::is('orders/month*') ? 'active' : '' }}"
                                   href="{{ route('orders.currentMonthOrders') }}">
                                    <div class="nav-link-icon"><i class="fa-solid fa-moon"></i></div>
                                    Current Month Orders
                                </a>
                                <a class="nav-link {{ Request::is('orders/year*') ? 'active' : '' }}"
                                   href="{{ route('orders.currentYearOrders') }}">
                                    <div class="nav-link-icon"><i class="fa-solid fa-sun"></i></div>
                                    Current Year Orders
                                </a>
                            </nav>
                        </div>
                        <a class="nav-link {{ Request::is('orders/due*') ? 'active' : '' }}"
                           href="{{ route('order.dueOrders') }}">
                            <div class="nav-link-icon"><i class="fa-solid fa-moon"></i></div>
                            Due Orders (Unpaid orders)
                        </a>
                        <a class="nav-link {{ Request::is('orders') ? 'active' : '' }}"
                           href="{{ route('orders.allOrders') }}">
                            <div class="nav-link-icon"><i class="fa-solid fa-moon"></i></div>
                            All Orders
                        </a>
                        <!-- Nested Sidenav Accordion (Pages -> People)-->
                        <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse"
                           data-bs-target="#pagesCollapseCashflow" aria-expanded="false"
                           aria-controls="pagesCollapseCashflow">
                            Cashflow Reports
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="pagesCollapseCashflow"
                             data-bs-parent="#accordionSidenavPagesCashflow">
                            <nav class="sidenav-menu-nested nav">
                                <a class="nav-link {{ Request::is('orders/report/cash*') ? 'active' : '' }}"
                                   href="{{ route('orders.dailyCashReport') }}">
                                    <div class="nav-link-icon"><i class="fa-solid fa-flag"></i></div>
                                    Daily Cash Report
                                </a>
                                <a class="nav-link {{ Request::is('orders/report/cash/current-week*') ? 'active' : '' }}"
                                   href="{{ route('orders.currentWeekCashReport') }}">
                                    <div class="nav-link-icon"><i class="fa-solid fa-calendar-week"></i></div>
                                    Current Week Cash
                                </a>
                                <a class="nav-link {{ Request::is('orders/report/cash/current-month*') ? 'active' : '' }}"
                                   href="{{ route('orders.currentMonthCashReport') }}">
                                    <div class="nav-link-icon"><i class="fa-solid fa-moon"></i></div>
                                    Current Month Cash
                                </a>
                                <a class="nav-link {{ Request::is('orders/report/cash/current-year*') ? 'active' : '' }}"
                                   href="{{ route('orders.currentYearCashReport') }}">
                                    <div class="nav-link-icon"><i class="fa-solid fa-sun"></i></div>
                                    Current Year Cash
                                </a>
                            </nav>
                        </div>

                        <a class="nav-link {{ Request::is('journals*') ? 'active' : '' }}"
                           href="{{ route('journals.index') }}">
                            <div class="nav-link-icon"><i class="fa-solid fa-circle-check"></i></div>
                            Journals
                        </a>
                    </nav>
                </div>
            @elseif(auth()->user()->role == "keeper")
                {{-- Store Keeper User --}}
                <!-- Sidenav Heading (Purchases)-->
                <div class="sidenav-menu-heading">Purchases</div>
                <a class="nav-link {{ Request::is('purchases/pending*') ? 'active' : '' }}"
                   href="{{ route('purchases.pendingPurchases') }}">
                    <div class="nav-link-icon"><i class="fa-solid fa-circle-check"></i></div>
                    Pending
                </a>
                <a class="nav-link {{ Request::is('purchases/approved*') ? 'active' : '' }}"
                   href="{{ route('purchases.approvedPurchases') }}">
                    <div class="nav-link-icon"><i class="fa-solid fa-circle-check"></i></div>
                    Approved
                </a>
                <a class="nav-link {{ Request::is('damages*') ? 'active' : '' }}"
                   href="{{ route('damages.allDamages') }}">
                    <div class="nav-link-icon"><i class="fa-solid fa-folder"></i></div>
                    Damages
                </a>
                <!-- Sidenav Heading (Orders)-->
                <div class="sidenav-menu-heading">Orders</div>
                <a class="nav-link {{ Request::is('orders/pending*') ? 'active' : '' }}"
                   href="{{ route('order.pendingOrders') }}">
                    <div class="nav-link-icon"><i class="fa-solid fa-circle-check"></i></div>
                    Approval
                </a>
                <a class="nav-link {{ Request::is('products*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                    <div class="nav-link-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
                    Products
                </a>
            @else
                {{-- Guest User --}}

                <!-- Sidenav Heading (Pages)-->
                <div class="sidenav-menu-heading">Pages</div>
            @endif
        </div>
    </div>

    <!-- Sidenav Footer-->
    <div class="sidenav-footer">
        <div class="sidenav-footer-content">
            <div class="sidenav-footer-subtitle">Logged in as:</div>
            <div class="sidenav-footer-title">{{ auth()->user()->name }}</div>
        </div>
    </div>
</nav>
