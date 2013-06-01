nimbus
======

Small, modular, closure-based dependency injection container.


Dependency injection in PHP is still a little weird. There are a lot of 
different styles. You can configure your context using YAML, XML, JSON and 
lots of other mechanisms. All of those need to parse some text to get their
stuff done. And there is Pimple, which is pretty cool! It uses anonymous
functions to create objects, you can set and invoke them using ArrayAccess, 
and you can create reusable sets of objects by subclassing it. 

