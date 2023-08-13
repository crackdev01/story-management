/**
 * Truncate string
 * @param {string} content content to be truncated
 * @param {number} maxLength max length of content to be truncated
 */

const truncateString = (content: string, maxLength: number = 80) => {
    if (content && content.length > maxLength) {
        return content.slice(0, maxLength) + '...';
    }
    return content;
}

export default truncateString;
