<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Shelfari 1.0</title>
        <link rel="stylesheet" href="static/css/master.css" type="text/css" media="screen" /> 


        
        <script src="static/js/jquery.js" type="text/javascript"></script>
        <script src="static/js/underscore.js" type="text/javascript"></script>
        <script src="static/js/backbone.js" type="text/javascript"></script>
        <script src="static/js/book_app.js" type="text/javascript"></script>
        
        <script type="text/javascript">
        $(document).ready(function()
        {
            var wid=$(window).width();
            var lef = Math.round((wid-$('#lightbox').width())/2)
            var ht=$(window).height();
            var to = Math.round((ht-$('#lightbox').height())/2)
            $('#lightbox').css('left',lef);
            $('#lightbox').css('top',to);
            
            
            
            $('#add_dialog').click(function(){
                $('#bg').css('opacity',0.3);
                $('#bg').fadeIn();
                $('#lightbox').fadeIn();
            });
            $('#cancel').click(function(){
                $('#bg').fadeOut();
                $('#lightbox').fadeOut();
            });
            
        })
        </script>
        
        
</head>

<body>
<div id="bg" style="display:none">
</div>
<div id="lightbox" style="display:none;">
	<h1>Add book</h1>

	<div id="body">

            <form id="bookform">
                <label for="book_name">Book name:</label>
                <input type="text" name="book_name" id="name" /><br/>
                <label for="book_author">Author name:</label>
                <input type="text" name="book_author" id="author" /><br/>
                <label for="book_status">Status:</label>
                <select id="status">
                <option  value="-1">Change Status</option>
                <option  value="1">Read</option>
                <option  value="0">Not Read</option>
                </select><br/> 
                <input type="button" value="Save" id="add" />
                <input type="button" value="Cancel" id="cancel" /><br/>
                
            </form>  

        </div>
</div>
<div id="container">
	<h1>Welcome to ShelFari 1.0!</h1>
        
	<div id="body" class="addnsearch">
                <p>
		<input type="button" id="add_dialog" name="add" value="Add Book" />
                Search Books:<input type="text" name="search_field" id="search_field" />
                <input type="button" id="search_btn" name="search" value="Search" />
                </p>
                
                <div id="inner_container">
                <div>
                <div class="list">
                <table cellspacing="0" width="100%" style="border:1px solid #D0D0D0" border="1px" id="heading">
                <tbody>
                <tr align="center" border="1px">
                    <th>Title</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                
                </tbody>
                </table>
        
                </div>
                    
                    
                <div class="list">
                    <table  cellspacing="0" width="100%" id="bookdata">
                        
                    </table>
                </div>
                </div>
                </div>
                
        </div>
        
        
        
        
        
	<p class="footer">Developed by Abhinav Koppula</p>
</div>
    
<script type="text/template" id="booktemplate">
    
        <td class="name">
          <%= name %>
        </td>
        <td class="author">
          <%= author %>
        </td>
        <td class="status">
            <% if(status==1) { %>Read<% } else { %>Not Yet Read<% }%>
        </td>
        <td style="float:right;">
            <input type="button" value="Edit" id="edit" />
        </td>
    
</script>
<script type="text/template" id="editbooktemplate">

	<h1>Edit book</h1>

	<div id="body">

            <form id="editbookform">
                <label for="book_name">Book name:</label>
                <input type="text" name="book_name" id="edit-name" value="<%= name %>" /><br/>
                <label for="book_author">Author name:</label>
                <input type="text" name="book_author" id="edit-author" value="<%= author %>" /><br/>
                <label for="book_status">Status:</label>
                <select id="edit-status">
                <option  value="-1">Change Status</option>
                <% if(status==1) { %>
                <option  value="1" selected>Read</option>
                <% } else { %>
                <option  value="1">Read</option>
                <% }%>
                <% if(status==0) { %>
                <option  value="0" selected>Not Read</option>
                <% } else { %>
                <option  value="0">Not Read</option>
                <% }%>
                </select><br/> 
                <input type="button" value="Edit" id="edit-save" />
                <input type="button" value="Cancel" id="edit-cancel" /><br/>
                <input type="button" value="Delete" id="delete" /><br/>
            </form>  

        </div>
</script>        
        <script type="text/javascript">
            
            var Book = Backbone.Model.extend({
                initialize:function(){
                    console.log('New Backbone Model');
                },
                url:'index.php/handle/',
                defaults:{
                    name:'',
                    author:'',
                    status:1 //1-read 0-not read
                }
                
            });
            
            var Directory = Backbone.Collection.extend({
                url:'index.php/book/get_all',
                model: Book,
                search: function(letters){
                   if(letters == "")
                        return this;
                    var pattern=new RegExp(letters,"gi");
                    return _(this.filter(function(data){
                        return pattern.test(data.get("name"));
                    }))
                }
            });
            
            var book_collection = new Directory();
            
            
            var BookView = Backbone.View.extend({
                tagName:"tr",
                template: $('#booktemplate').html(),
                edittemplate: _.template($('#editbooktemplate').html()),
                initialize:function()
                {
                    console.log('New BookView created');
                },
                events:{
                    "click #edit" : "editbook",
                    "click #edit-cancel" : "canceledit",
                    "click #delete" : "deletebook",
                    "click #edit-save" : "editbooksave"
                },
                editbook:function(){
                    this.$el.html(this.edittemplate(this.model.toJSON()));
                    
                },
                canceledit: function(){
                    this.render();
                },
                deletebook: function(){
                    this.$el.fadeOut(200);
                    id=this.model.get('id');
                    $.ajax({
                                url:'index.php/handle',
                                type:'DELETE',
                                data:{'id':id},
                                success:function(){
                                    
                                }
                                
                    });
                    
                    this.model.destroy({
                        urlRoot:"index.php/handle/",
                        
                        success:function(){
                            alert('Book Deleted');
                        }
                    });
                },
                editbooksave: function(){
                    savename=$('#edit-name').val();
                    saveauthor=$('#edit-author').val();
                    savestatus=$('#edit-status').find(':selected').val();
                    if(savename!='' && saveauthor!='' && savestatus!=-1)
                    {
                        saveflag=1;
                    }
                    else
                    {
                            alert('One or more fields are blank');
                            saveflag=0;
                    }
                    if(saveflag){
                        this.model.set({name:savename,author:saveauthor,status:savestatus});
                        id=this.model.get('id');
                        var bookmodel=new Book();
                        bookmodel.save({
                            id:id,
                            name:savename,
                            author:saveauthor,
                            status:savestatus
                        })
                        
                        alert('Changes saved');
                        this.render();
                    }
                },
                render: function()
                {
                    var tmpl = _.template(this.template);
                    this.$el.html(tmpl(this.model.toJSON()));
                    return this;
                }
            });
            
            var DirectoryView = Backbone.View.extend({
                el:$('#bookdata'),
                
                initialize:function(){
                    self=this;
                    book_collection.fetch({
                    error: function () {
                    console.log("error");
                    },
                    success: function () {
                    console.log("no error"); 
                    }
                    }).complete(function () {
                    self.collection = book_collection;
                    self.render();
                    
                    self.collection.on('add',self.renderBook,self);
                    });
                    
                    
                    
                },
                render:function(){
                    var that=this;
                    _.each(this.collection.models,function(item){
                        that.renderBook(item);
                    },this);
                },
                renderBook:function(item){
                    var bookView=new BookView({
                        model:item
                    });
                    this.$el.append(bookView.render().el);
                }
                
                
            });
            
            
            var directory=new DirectoryView();
            
            var AddBookView = Backbone.View.extend({
               el:$('#bookform'),
               initialize:function(){
                   console.log('Book Form Initialized');
                   this.collection=book_collection;
               },
               events : {
                    "click #add" : "addBook"
               },
               addBook: function(e)
               {
                    e.preventDefault();
                    var newmodel = {};
                    name=$('#name').val();
                    author=$('#author').val();
                    status=$('#status').find(':selected').val();
                    if(name!='' && author!='' && status!=-1)
                    {
                        flag=1;
                    }
                    else
                    {
                            alert('One or more fields are blank');
                            flag=0;
                    }
                    if(flag){
                        newmodel['name']=name;
                        newmodel['author']=author;
                        newmodel['status']=status;
                        
                        
                        var bookModel = new Book();
                        $.ajax({
                            type:'POST',
                            async:false,
                            url:'index.php/handle',
                            data:{'name':name,'author':author,'status':status},
                            success:function(response){
                                newmodel['id']=response;
                                books.push(newmodel);
                                console.log(newmodel['id']);
                                
                            }
                            
                        })
                        
                        var newbookmodel = new Book(newmodel);
                        this.collection.add(newbookmodel);
                        $('#lightbox').fadeOut();
                        $('#bg').fadeOut();
                        
                        
                        
                    }
                    
               }
            });
            var addbookview=new AddBookView();
            
            var SearchBookView = Backbone.View.extend({
                el:$('#body.addnsearch'),
                events:{
                    'click #search_btn':'search_book'
                },
                initialize:function(){
                    this.collection=book_collection; 
                    console.log(this.collection.models);
                },
                renderBook:function(item){
                    var bookView=new BookView({
                        model:item
                    });
                    
                    $('#bookdata').append(bookView.render().el);
                },
                search_book:function(){
                    var letters=$('#search_field').val();
                    var pattern=new RegExp(letters,"gi");
                    var search_coll=[];
                    $('#bookdata').html('');
                    _.each(this.collection.models,function(item){
                        if(pattern.test(item.get('name')))
                        {
                                search_coll.push(item);
                                console.log(item.get('author'));
                                this.renderBook(item);
                        }
                    },this);
                    
                }
            })
            var searchbookview=new SearchBookView();
        </script>
    
    
</body>
</html>