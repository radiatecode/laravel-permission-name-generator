<div class="row">
    <div class="col-md-12 col-lg-12 col-sm-12">
        <div class="permissions-buttons-card" style="padding: 8px;border: 1px solid #a09c9c;height: 55px;margin-bottom: 10px; background-color: #e8f1f4;border-radius: 5px;">
            <div class="role" style="float: left; padding-left: 8px;"><label>Role: {{ $roleName }}</label></div>
            <div class="permission-buttons" style="float: right;padding-right: 8px;">
               {!! implode(' ',$permissionButtons) !!}
            </div>
        </div>
    </div>
    @foreach($routes as $key => $values)
    <div class="{{ config('route-permission.card-size-class') }}">
        <div class="card permission-card">
            <div class="card-header permission-header">
                <div class="card-title">
                    <div class="permission-title" style="display: flex;">
                        <div style="margin-right: 8px;">
                            <input type="checkbox" class="head-checkbox" id="{{ $key }}"/>
                        </div>
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
                                    <input type="checkbox" name="privileges[]" value="{{ $route['route'] }}" id="{{ $route['route'] }}" {{ in_array($route['route'],$rolePermissions) ? 'checked' : '' }}>
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
</div>