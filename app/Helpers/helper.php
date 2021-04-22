<?php


function FormText($title, $name, $value = null, $attribute = [])
{
    $value = old($name) ?? $value;
    $html_attr = [];
    if ($attribute != []) {
        foreach ($attribute as $attr_k => $attr_v) {
            $html_attr[] = "$attr_k = '$attr_v'";
        }
        $html_attr = implode(' ', $html_attr);
    } else {
        $html_attr =  ' class="form-control" ';
    }
    $html = '
        <div class="form-group">
            <label class="">' . $title . '</label>
            <input type="text" name="' . $name . '"  value="' . $value . '" ' . $html_attr . '>
        </div>
    ';
    return $html;
}

function FormTime($title, $name, $value = null, $attribute = [])
{
    $value = old($name) ?? $value;
    $html_attr = [];
    if ($attribute != []) {
        foreach ($attribute as $attr_k => $attr_v) {
            $html_attr[] = "$attr_k = '$attr_v'";
        }
        $html_attr = implode(' ', $html_attr);
    } else {
        $html_attr =  ' class="form-control" ';
    }
    $html = '
        <div class="form-group">
            <label class="">' . $title . '</label>
            <input type="time" name="' . $name . '"  value="' . $value . '" ' . $html_attr . '>
        </div>
    ';
    return $html;
}

function FormCheckbox($title, $name, $value = null, $attribute = [])
{
    $value = old($name) ?? $value;
    $html_attr = [];
    if ($attribute != []) {
        foreach ($attribute as $attr_k => $attr_v) {
            $html_attr[] = "$attr_k = '$attr_v'";
        }
        $html_attr = implode(' ', $html_attr);
    } else {
        $html_attr =  ' class="big-check" ';
    }
    $checked = $value == 1 ? 'checked' :  '';
    $html = '
        <div class="form-group">
            <label class="">' . $title . '</label>
            <div class="">
                <input type="hidden" name="' . $name . '"  value="' . 0 . '" ' . $html_attr . '>
                <input type="checkbox" name="' . $name . '"  value="' . 1 . '" ' . $html_attr . '' . $checked . ' >
            </div>
        </div>
    ';
    return $html;
}

function FormPassword($title, $name, $value = null, $attribute = [])
{
    $value = old($name) ?? $value;
    $html_attr = [];
    if ($attribute != []) {
        foreach ($attribute as $attr_k => $attr_v) {
            $html_attr[] = "$attr_k = '$attr_v'";
        }
        $html_attr = implode(' ', $html_attr);
    } else {
        $html_attr =  ' class="form-control" ';
    }
    $html = '
        <div class="form-group">
            <label class="">' . $title . '</label>
            <input type="password" name="' . $name . '"  value="' . $value . '" ' . $html_attr . '>
        </div>
    ';
    return $html;
}

function FormDate($title, $name, $value = null, $attribute = [])
{
    $value = old($name) ?? $value;
    $html = '
        <div class="form-group">
            <label class="">' . $title . '</label>
            <input type="date" name="' . $name . '" class="form-control" value="' . $value . '">
        </div>
    ';
    return $html;
}

function FormFile($title, $name, $value = null, $attribute = [])
{
    $placeholder_path = asset('assets/img/image_placeholder.jpg');
    if ($value != null) {
        $image_path = FileHandler::get_file_url($value);
    } else {
        $image_path = $placeholder_path;
    }

    $html = '
    <div class="">
        <label class="row col-md-12">' . $title . '</label>
        <div class="fileinput fileinput-new text-center" data-provides="fileinput">
            <div class="fileinput-new thumbnail">
                <img src="' . $image_path . '" alt="placeholder">
            </div>
            <div class="fileinput-preview fileinput-exists thumbnail" style=""></div>
            <div class="fileinput-new-btn">
                <span class="btn btn-rose btn-round btn-file">
                <span class="fileinput-new ">Select image</span>
                <span class="fileinput-exists">Change</span>
                <input type="hidden"><input type="file" name="' . $name . '">
                <div class="ripple-container"></div></span>
                <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
            </div>
        </div>
    </div>
    ';
    return $html;
}

function FormTextarea($title, $name, $value = null, $attribute = [])
{
    $value = old($name) ?? $value;
    $html = '
        <div class="form-group">
            <label class="">' . $title . '</label>
            <textarea name="' . $name . '" class="form-control">' . $value . '</textarea>
        </div>
    ';
    return $html;
}


function FormSelect($title, $name, $options, $value = null, $attribute = [])
{
    $value = old($name) ?? $value;

    $html_options = '<option value="">-</option>';
    foreach ($options as $k => $v) {
        $selected = $k == $value ? 'selected' : '';
        $html_options .= "<option value='$k' $selected>$v</option>";
    }

    $html_attr = [];
    if ($attribute != []) {
        foreach ($attribute as $attr_k => $attr_v) {
            $html_attr[] = "$attr_k = '$attr_v'";
        }
        $html_attr = implode(' ', $html_attr);
    } else {
        $html_attr =  ' class="form-control" ';
    }



    $html = '
        <div class="form-group">
            <label class="">' . $title . '</label>
            <select name="' . $name . '" ' . $html_attr . ' >
                ' . $html_options . '
            </select>
        </div>
    ';
    return $html;
}

function InputSelect($name, $options, $value = null, $attribute = [])
{
    $value = old($name) ?? $value;

    $html_options = '<option>-</option>';
    foreach ($options as $k => $v) {
        $selected = $k == $value ? 'selected' : '';
        $html_options .= "<option value='$k' $selected>$v</option>";
    }

    $html = '
        <select name="' . $name . '" class="form-control">
            ' . $html_options . '
        </select>
    ';
    return $html;
}
