import axios from 'axios';

export default class Like
{
    constructor(likeElements) {
        this.likeElements = likeElements;

        if(this.likeElements)
        {
            this.init();
        }
    }

    init() {
        this.likeElements.map(element => {
            element.addEventListener('click', this.onClick)
        })
    }
    
    /* Click updates Likes of the Post */
    onClick(event) {
        event.preventDefault();

        const url = this.href;

        axios.get(url).then(res => {

            /* Number Likes */
            const nb = res.data.nbLike;
            const span = this.querySelector('span');

            this.dataset.nb = nb;
            if ( nb <= 1)
            {
                span.innerHTML = nb + ' J\'aime';
            }
            else
            {
                span.innerHTML = nb + ' J\'aimes';
            }

            /* SVG Heart */
            const thumbsUpFilled = this.querySelector('svg.fill-red-500');
            const thumbsUpUnFilled = this.querySelector('svg.unfilled');

            thumbsUpFilled.classList.toggle('hidden');
            thumbsUpUnFilled.classList.toggle('hidden');

        })
    }
}