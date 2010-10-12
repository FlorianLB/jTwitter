<div class="twitter-timeline">
     <p>{$user}</p>
     <ul>
          {foreach $timeline as $item}
          <li><span class="date">{$item['date']}</span>
               <p>{$item['text']}</p>
          </li>
          {/foreach}
     </ul>
</div>