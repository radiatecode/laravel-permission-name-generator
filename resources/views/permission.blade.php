@foreach($routes as $key => $values)
    <div class="{{ config('route-permission.card-size-class') }}">
        <div class="card permission-card">
            <div class="card-header">
                <div class="card-title">
                    <div class="permission-header" style="display: flex;">
                        <div style="margin-right: 8px;"><input type="checkbox" class="head-checkbox" id="{{ $key }}"/></div>
                        <div class="permission-title" style="font-family: inherit;font-weight: bold;">{{ ucwords(str_replace('-', ' ', $key)) }}</div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="{{ $key }}-routes-checkbox">
                        <ul style="list-style-type: none">
                           @foreach($values as $route)
                                <li>
                                    <input type="checkbox" name="role_access[]" value="{{ $route['route'] }}" id="{{ $route['route'] }}">
                                    <label class="form-check-label" for="{{ $route['route'] }}">{{ $route['title'] }}</label>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach