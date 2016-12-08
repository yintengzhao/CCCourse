<% if ENV['NANOC_ENV']=='production' && ENV['NANOC_REMOTE'] %>
@ng_app.constant('SERVER_HOST', "//115.159.125.112:1122/index.php" );
<% else %>
@ng_app.constant('SERVER_HOST', "//127.0.0.1:1122" );
<% end %>
