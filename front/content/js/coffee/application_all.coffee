<% if ENV['NANOC_ENV']=='production' %>
<%= JSPipeline.pipe_all(@items, @item) %>
<% end %>