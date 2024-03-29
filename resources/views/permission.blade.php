<div class="row">
    <div class="col-md-12 col-lg-12 col-sm-12">
        <div class="permissions-buttons-card"
            style="padding: 8px;border: 1px solid #a09c9c;height: 55px;margin-bottom: 10px; background-color: #e8f1f4;border-radius: 5px;">
            <div class="role" style="float: left; padding-left: 8px;"><label>Role: {{ $roleName }}</label></div>
            <div class="permission-buttons" style="float: right;padding-right: 8px;">
                <button type="button" class="btn btn-primary" onclick="checkAll()"><i class="fa fa-check-square"></i>
                    Check All</button>
                <button type="button" class="btn btn-warning" onclick="uncheckAll()"><i class="fa fa-square"></i>
                    Uncheck All </button>
                <button type="button" class="btn btn-success save-btn" onclick="saveRolePermissions()"
                    title="save role permission"><i class="save-loader fa fa-save"></i> Save </button>
            </div>
        </div>
    </div>

    @foreach ($permissions as $key => $values)
        <!-- section wise permissions -->
        @if (array_key_exists('section', $values))
            <div class="col-md-12 col-lg-12 col-sm-12">
                <div class="card card-info">
                    <div class="card-header">
                        <div class="card-title">{{ ucwords($values['section']) }}</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($values['permissions'] as $permissionsKey => $permissions)
                                <div class="col-md-3 col-lg-3 col-sm-12">
                                    <div class="card permission-card" style="background-color: #f0f1f0">
                                        <div class="card-header permission-header">
                                            <div class="card-title">
                                                <div class="permission-title" style="display: flex;">
                                                    <div style="margin-right: 8px;">
                                                        <input type="checkbox" name="head_checkbox" class="head-checkbox" id="{{ $permissionsKey }}" />
                                                    </div>
                                                    <div class="permission-title" style="font-family: inherit;font-weight: bold;">
                                                        {{ ucwords(str_replace('-', ' ', $permissionsKey)) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="{{ $key }}-permissions-checkbox">
                                                    <ul style="list-style-type: none">
                                                        @foreach ($permissions as $permission)
                                                            <li>
                                                                <input type="checkbox" name="permissions[]" value="{{ $permission['name'] }}"
                                                                    id="{{ $permission['name'] }}" {{ in_array($permission['name'], $rolePermissions) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="{{ $permission['name'] }}">{{ $permission['text'] }}</label>
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
                    </div>
                </div>
            </div>
        @else
            <!-- permissions without section -->
            <div class="col-md-3 col-lg-3 col-sm-12">
                <div class="card permission-card" style="background-color: #f0f1f0">
                    <div class="card-header permission-header">
                        <div class="card-title">
                            <div class="permission-title" style="display: flex;">
                                <div style="margin-right: 8px;">
                                    <input type="checkbox" name="head_checkbox" class="head-checkbox" id="{{ $key }}" />
                                </div>
                                <div class="permission-title" style="font-family: inherit;font-weight: bold;">{{ ucwords(str_replace('-', ' ', $key)) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="{{ $key }}-permissions-checkbox">
                                <ul style="list-style-type: none">
                                    @foreach ($values as $permission)
                                        <li>
                                            <input type="checkbox" name="permissions[]" value="{{ $permission['name'] }}" id="{{ $permission['name'] }}"
                                                {{ in_array($permission['name'], $rolePermissions) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="{{ $permission['name'] }}">{{ $permission['text'] }}</label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>
